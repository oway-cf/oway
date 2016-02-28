<?php

namespace App\Models;

use akeinhell\Api2Gis;
use akeinhell\RequestParams\CarRouteParams;

class Way
{
    public static function build(TodoList $list)
    {
        list($itemsWithPoint, $itemsWithoutPoint) = Graph::separationItems(
            Graph::calculateItemsPoints($list->todoListItems)
        );

        $routeData = static::getGisRoute($itemsWithPoint);

        return [
            'total_distance' => current(current($routeData)->items)->total_distance,
            'total_duration' => current(current($routeData)->items)->total_duration,
            'points'         => $itemsWithPoint,
            'bad_points'     => $itemsWithoutPoint,
            'paths'          => static::recursivePrepareData($routeData),
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