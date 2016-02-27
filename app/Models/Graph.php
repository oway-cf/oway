<?php

namespace App\Models;

use akeinhell\Api2Gis;
use akeinhell\RequestParams\CarRouteParams;
use Cache;

class Graph
{
    /**
     * @param TodoListItem[] $listItems
     *
     * @return TodoListItem[]
     */
    public static function calculateItemsPoints($listItems = [])
    {
        return static::sortGraph($listItems);
    }

    protected static function sortGraph($listItems)
    {
        $initial = [];
        $ways    = [];
        $cost    = 1000000000;

        for ($i = 1; $i < count($listItems) - 1; $i++) {
            $initial[] = $i;
        }

        while (!static::isDescSort($initial)) {
            $ways[]  = $initial;
            $initial = static::getNextPermutation($initial);
        }
        $ways[] = $initial;

        for ($i = 0; $i < count($ways); $i++) {
            $way     = array_merge([0], $ways[$i]);
            $way[]   = (count($listItems) - 1);
            $wayCost = 0;
            for ($j = 0; $j < count($way) - 1; $j++) {
                $edge = static::getEdge($listItems[$way[$i]], $listItems[$way[$i + 1]]);
                $wayCost += $edge[0]->items[0]->total_distance;
            }
            if ($wayCost < $cost) {
                $cost = $wayCost;
                $initial = $way;
            }
        }

        $out = [];
        for ($j = 0; $j < count($way); $j++) {
            $out[] = $listItems[$initial[$i]];
        }

        return $out;
    }

    protected static function isDescSort($arr)
    {
        for ($i = 0; $i < count($arr) - 1; $i++) {
            if ($arr[$i] < $arr[$i + 1]) {
                return false;
            }
        }

        return true;
    }

    protected static function getNextPermutation($arr)
    {
        for ($i = count($arr) - 2; $i >= 0; $i--) {
            if ($arr[$i] < $arr[$i + 1]) {
                $min    = $arr[$i + 1];
                $minPos = $i + 1;
                for ($j = $minPos; $j < count($arr); $j++) {
                    if ($arr[$j] < $min && $arr[$j] > $arr[$i]) {
                        $min    = $arr[$j];
                        $minPos = $j;
                    }
                }
                $arr = static::changeArrayElements($arr, $i, $minPos);
                for ($j = $i + 1; $j < count($arr) - 1; $j++) {
                    for ($k = $j + 1; $k < count($arr); $k++) {
                        if ($arr[$j] > $arr[$k]) {
                            $arr = static::changeArrayElements($arr, $j, $k);
                        }
                    }
                }
                break;
            }
        }

        return $arr;
    }

    protected static function changeArrayElements($arr, $i, $j)
    {
        $tmp     = $arr[$j];
        $arr[$j] = $arr[$i];
        $arr[$i] = $tmp;

        return $arr;
    }

    protected static function getEdge($point1, $point2)
    {
        $cacheKey = static::getEdgeCacheKey($point1, $point2);

        if (!($edge = Cache::get($cacheKey))) {
            $routePoints = new CarRouteParams();
            $routePoints->addWaypoint([$point1->lon, $point1->lat]);
            $routePoints->addWaypoint([$point2->lon, $point2->lat]);
            Api2Gis::call()->CarRouteDirectionsAsync($routePoints);
            $edge = Api2Gis::call()->execute();
            Cache::put($cacheKey, $edge, 30);
        }

        return $edge;
    }

    protected static function getEdgeCacheKey($point1, $point2)
    {
        $key = sprintf('edge_%s_%s_to_%s_%s', $point1->lon, $point1->lat, $point2->lon, $point2->lat);

        return $key;
    }
}