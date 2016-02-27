<?php

namespace App\Http\Controllers;

use akeinhell\Api2Gis;
use akeinhell\RequestParams\CarRouteParams;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WayController extends Controller
{
    public function build($id)
    {
        $start = ["54.985059", "82.897046"];
        $coords = [
            ["54.985059", "82.897046"],
            ["55.033084", "82.920115"],
            ["55.041993", "82.949499"],
            ["55.054526", "82.893543"],
            ["55.028574", "82.936429"],
        ];


    }

    private function calc($start, $coords)
    {
        foreach ($coords as $coord) {
            $params = new CarRouteParams();
            $params
                ->addWaypoint($start)
                ->addWaypoint($coord);
            Api2Gis::call()->CarRouteDirectionsAsync($params);
        }
        return Api2Gis::call()->execute();
    }
}
