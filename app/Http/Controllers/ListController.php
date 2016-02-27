<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;

class ListController extends Controller
{
    private $mockItem = [
        "id" => 1,
        "todo_list_id" => 1,
        "title" => "string",
        "type" => "geo_point",
        "position" => 0,
        "after" => 1,
        "created_at" => "2016-02-27 11:00:11",
        "updated_at" => "2016-02-27 11:00:11",
        "lon" => "55.55555",
        "lat" => "88.88888",
    ];

    public function create()
    {
        return response()
            ->json([
                'key' => 1
            ])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function update($id)
    {
        return response()
            ->json([
                'key' => 1
            ])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function show($id)
    {
        return response()
            ->json([
                'todo_list_items' => [
                    $this->mockItem,
                    $this->mockItem,
                    $this->mockItem,
                    $this->mockItem,
                    $this->mockItem,
                    $this->mockItem,
                ]
            ])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function buildWay($id)
    {
    }
}
