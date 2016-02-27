<?php

namespace App\Models;

use akeinhell\Api2Gis;
use akeinhell\RequestParams\CarRouteParams;

class Way
{
    public static function build(TodoList $list)
    {
        $listItems = $list->todoListItems;
        $points    = Graph::calculateItemsPoints($listItems);

        return [
            'total_distance' => 123,
            'total_duration' => 23,
            'points'         => $points,
            'paths'          => static::recursivePrepareData(static::getGisRoute($points)),
        ];
    }

    private static function recursivePrepareData($items)
    {
        $result = [];

        if (is_array($items)) {
            foreach ($items as $item) {
                $result = array_merge($result, static::recursivePrepareData($item));
            }
        } elseif (is_object($items)) {
            if (!empty($items->selection)) {
                return [$items->selection];
            }
            foreach ($items as $item) {
                $result = array_merge($result, static::recursivePrepareData($item));
            }
        }

        return $result;
    }

    /**
     * @param TodoListItem[] $points
     * @return \akeinhell\Types\GisResponse[]
     */
    private static function getGisRoute($points)
    {
        if (empty($points)) {
            return [];
        }

        $routePoints = new CarRouteParams();

        foreach ($points as $point) {
            $routePoints->addWaypoint([$point->lon, $point->lat]);
        }

        Api2Gis::call()->CarRouteDirectionsAsync($routePoints);

        return Api2Gis::call()->execute();
    }
}