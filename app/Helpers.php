<?php

use App\Models\DynamicRoute;

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
    $i        = 1;
    while (!$trueSlug) {
        $slug = str_replace(md5('/'), '/', str_slug($string));
        $slugEx = explode('-',$string);
        $number = (int)end($slugEx);
        if ($number > 0) {
            $i += $number+1;
        }

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
