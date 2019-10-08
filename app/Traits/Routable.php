<?php

namespace App\Traits;

use \App\Models\DynamicRoute;

trait Routable
{
    /**
     * Url oluşturulması istenilen model içerisinde çağırılır
     */
    protected static function bootRoutable()
    {
        $calledModel = get_called_class();
        self::saved(function($item) use ($calledModel) {
            self::routeSave($item, $calledModel);
            self::clearBootedModels();
        });

        self::deleting(function($item) use ($calledModel) {
            self::routeDelete($item, $calledModel);
            self::clearBootedModels();
        });
    }

    /**
     * Route verisini getirir
     *
     * @return mixed
     */
    public function route()
    {
        return $this->hasOne(DynamicRoute::class, 'parameter', 'id')->where('namespace', $this->frontNameSpace);
    }

    /**
     * Route içerisinde arama yapmayı sağlar
     *
     * @param $query
     * @param string $slug
     * @return mixed
     */
    public function scopeWhereSlug($query, $slug = '')
    {
        return $query->whereHas("route", function($q) use ($slug) {
            $q->where('routes.slug', '=', $slug);
        });
    }

    /**
     * @param $item
     * @param $calledModel
     * @return bool
     */
    public static function routeSave($item, $calledModel)
    {
        $colum = $item->slugColum;
        $slug  = checkSlug($item->$colum, $item->route_id);
        #Prefix
        if (isset($item->slugPrefix) && $item->slugPrefix) {
            $prefix    = $item->slugPrefix.'/';
            $prefixLen = strlen($prefix);
            if (substr($slug, 0, $prefixLen) == $prefix) {
                $slug = substr($slug, $prefixLen);
            }
            $slug = $prefix.$slug;
        }

        $route = DynamicRoute::findOrNew($item->route_id);

        $route->namespace = $item->frontNameSpace;
        $route->parameter = $item->id;
        $route->slug      = $slug;
        if (isset($item->title)) {
            $route->title = $item->title;
        }
        if (isset($item->description)) {
            $route->description = $item->description;
        }
        if (isset($item->keywords)) {
            $route->keywords = $item->keywords;
        }

        try {
            $route->save();
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                $route->slug = $slug.'_'.uniqid();
                $route->save();
            } else {
                return false;
            }
        }

        if ($item->route_id != $route->id) {
            $calledModel::flushEventListeners();
            try {
                $item->route_id = $route->id;
                $item->save();
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param $item
     * @param $calledModel
     */
    public static function routeDelete($item, $calledModel)
    {
        $route = DynamicRoute::find($item->route_id);
        if ($route) {
            $route->delete();
        }

    }
}

/**
 * Slug başka bir içerikte kullanılıyormu kontrol eder
 *
 * @param $stringRaw
 * @param null $routeId
 * @param null $parameterId
 * @return string
 */
function checkSlug($stringRaw, $routeId = NULL, $parameterId = NULL)
{
    $trueSlug = false;
    $string   = str_replace('/', md5('/'), $stringRaw);
    $i        = 2;
    while (!$trueSlug) {
        $slug = str_replace(md5('/'), '/', str_slug($string));
        if ($parameterId) {
            $slugData = DynamicRoute::select('slug')->where('parameter', '!=', $parameterId)->where('slug', $slug)->limit(1)->first();
        } else {
            $slugData = DynamicRoute::select('slug')->where('id', '!=', $routeId)->where('slug', $slug)->limit(1)->first();
        }

        if (!$slugData) {
            $trueSlug = true;
        } else {
            $string = $stringRaw.' '.$i;
            $i++;
        }
    }

    return $slug;
}
