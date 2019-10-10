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
        return $this->hasOne(DynamicRoute::class, 'parameter', 'id')->where('namespace', $this->frontNameSpace)->withDefault();
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
        $routeId = NULL;
        if (isset($item->route->id)) {
            $routeId = $item->route->id;
        }

        $slug = request()->input('route.slug', $item->{$item->slugColum});
        $slug = checkSlug($slug, $routeId);

        #Prefix
        if (isset($item->slugPrefix) && $item->slugPrefix) {
            $prefix    = $item->slugPrefix.'/';
            $prefixLen = strlen($prefix);
            if (substr($slug, 0, $prefixLen) == $prefix) {
                $slug = substr($slug, $prefixLen);
            }
            $slug = $prefix.$slug;
        }

        $route = DynamicRoute::findOrNew($routeId);

        $route->namespace   = $item->frontNameSpace;
        $route->parameter   = $item->id;
        $route->slug        = $slug;
        $route->title       = request()->input('route.title');
        $route->description = request()->input('route.description');
        $route->keywords    = request()->input('route.keywords');

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
