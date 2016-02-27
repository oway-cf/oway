<?php

namespace App\Models;

use Cache;

class Graph
{
    /**
     * @param TodoListItem[] $listItems
     * @return TodoListItem[]
     */
    public static function calculateItemsPoints($listItems = [])
    {
        return $listItems;
        return [
            [
                'title' => 'Julius Meinl',
                'address' => 'Линейная, 37/2',
                'lon' => "82.910206",
                'lat' => "55.04982",
            ],
            [
                'title' => 'Кофейные технологии, ООО, торгово-сервисная компания',
                'address' => 'Писарева, 60',
                'lon' => "82.903137",
                'lat' => "55.060355",
            ],
            [
                'title' => 'Колибри, ООО, торговая компания',
                'address' => 'Дачная, 21/1',
                'lon' => "82.954886",
                'lat' => "55.013059",
            ],
        ];
    }

    protected static function sortGraph(array $listItems)
    {
        $initial = [];
        $ways    = [];
        $cost    = 0;

        for ($i = 1; $i < count($listItems) - 1; $i++) {
            $initial[] = $i;
        }

        while (!static::isDescSort($initial)) {
            $ways[] = $initial;
            $initial = static::getNextPermutation($initial);
        }
        $ways[] = $initial;

        return $ways;
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

        if ($edge = Cache::get($cacheKey)) {
            $routePoints = new CarRouteParams();
            $routePoints->addWaypoint($point1);
            $routePoints->addWaypoint($point2);
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