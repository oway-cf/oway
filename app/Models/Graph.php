<?php

namespace App\Models;

use Cache;

class Graph
{
    /**
     * @param \TodoListItems[] $listItems
     * @return \TodoListItems[]
     */
    public static function calculateItemsPoints(array $listItems = [])
    {
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