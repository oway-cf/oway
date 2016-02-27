<?php

namespace App\Http\Controllers;

use akeinhell\Api2Gis;
use akeinhell\RequestParams\CarRouteParams;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WayController extends Controller
{
    private static function examplePoints()
    {
        return [
            ["54.985059", "82.897046"],
            ["55.033084", "82.920115"],
            ["55.041993", "82.949499"],
            ["55.054526", "82.893543"],
            ["55.028574", "82.936429"],
        ];
    }

    public function show($id)
    {
        return static::getRouteData(static::examplePoints());
    }

    private function getRouteData($points)
    {
        $routePoints = new CarRouteParams();

        foreach ($points as $point) {
            $routePoints->addWaypoint($point);
            Api2Gis::call()->CarRouteDirectionsAsync($routePoints);
        }

        return Api2Gis::call()->execute();
    }
}
