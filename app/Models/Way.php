<?php

namespace App\Models;

use akeinhell\Api2Gis;
use akeinhell\RequestParams\CarRouteParams;

class Way
{
    public static function build(TodoList $list)
    {
        $points = $list->getPoints();
        $routePoints = new CarRouteParams();

        foreach ($points as $point) {
            $routePoints->addWaypoint($point);
            Api2Gis::call()->CarRouteDirectionsAsync($routePoints);
        }

        return Api2Gis::call()->execute();
    }
} 