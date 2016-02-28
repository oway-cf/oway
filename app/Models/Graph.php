<?php

namespace App\Models;

use akeinhell\Api2Gis;
use akeinhell\Exceptions\GisRequestException;
use akeinhell\RequestParams\CarRouteParams;
use App\API\YandexConnector;
use Cache;

/**
 * Class Graph
 * @package App\Models
 */
class Graph
{
    /**
     * @param TodoListItem[] $listItems
     *
     * @return TodoListItem[]
     */
    public static function calculateItemsPoints($listItems = [])
    {
        list($itemsWithPoint, $itemsWithoutPoint) = static::separationItems($listItems);

        $area        = static::getAreaByPoint($itemsWithPoint);
        $updatedItem = static::findBestPointForCategoryInArea($area, $itemsWithoutPoint);

        return static::sortGraph($listItems);
    }

    /**
     * @return string
     */
    public static function getAreaByPoint($listItems)
    {
        $items    = static::sortGraph($listItems);
        $polygons = [];
        for ($i = 0; $i < count($items) - 1; $i++) {
            $polygons[] = static::getPolygonAroundPoints($items[$i], $items[$i + 1]);
        }

        return "POLYGON(" . implode(",", $polygons) . ")";
    }

    private static function getPolygonAroundPoints($point1, $point2)
    {
        $points = [];
        $delta  = 0.01;

        $x1 = (double)$point1->lon;
        $y1 = (double)$point1->lat;
        $x2 = (double)$point2->lon;
        $y2 = (double)$point2->lat;
        if ($x1 > $x2) {
            $tmp1 = $x1;
            $tmp2 = $y1;
            $x1   = $x2;
            $y1   = $y2;
            $x2   = $tmp1;
            $y2   = $tmp2;
        }

        if ($y1 < $y2) {
            $x        = $x1 - $delta;
            $y        = $y1 - $delta;
            $points[] = "{$x} {$y}";

            $x        = $x1 - $delta;
            $y        = $y1 + $delta;
            $points[] = "{$x} {$y}";

            $x        = $x2 - $delta;
            $y        = $y2 + $delta;
            $points[] = "{$x} {$y}";

            $x        = $x2 + $delta;
            $y        = $y2 + $delta;
            $points[] = "{$x} {$y}";

            $x        = $x2 + $delta;
            $y        = $y2 - $delta;
            $points[] = "{$x} {$y}";

            $x        = $x1 + $delta;
            $y        = $y1 - $delta;
            $points[] = "{$x} {$y}";

            $x        = $x1 - $delta;
            $y        = $y1 - $delta;
            $points[] = "{$x} {$y}";
        } else {
            $x        = $x1 - $delta;
            $y        = $y1 - $delta;
            $points[] = "{$x} {$y}";

            $x        = $x1 - $delta;
            $y        = $y1 + $delta;
            $points[] = "{$x} {$y}";

            $x        = $x1 + $delta;
            $y        = $y1 + $delta;
            $points[] = "{$x} {$y}";

            $x        = $x2 + $delta;
            $y        = $y2 + $delta;
            $points[] = "{$x} {$y}";

            $x        = $x2 + $delta;
            $y        = $y2 - $delta;
            $points[] = "{$x} {$y}";

            $x        = $x2 - $delta;
            $y        = $y2 - $delta;
            $points[] = "{$x} {$y}";

            $x        = $x1 - $delta;
            $y        = $y1 - $delta;
            $points[] = "{$x} {$y}";
        }

        return '(' . implode(',', $points) . ')';
    }

    /**
     * @param TodoListItem[] $listItems
     *
     * @return array
     */
    public static function separationItems($listItems)
    {
        $itemsWithPoint    = [];
        $itemsWithoutPoint = [];

        foreach ($listItems as $item) {
            if ($item->lat && $item->lon) {
                $itemsWithPoint[] = $item;
            } else {
                $itemsWithoutPoint[] = $item;
            }
        }

        return [$itemsWithPoint, $itemsWithoutPoint];
    }

    /**
     * @param $itemsWithoutPoint
     * @param $polygon
     *
     * @return mixed
     * @throws GisRequestException
     * @throws \HttpResponseException
     */
    public static function findBestPointForCategoryInArea($polygon, $itemsWithoutPoint)
    {
        try {
            foreach ($itemsWithoutPoint as $item) {
                $getRubric = static::prepareData(YandexConnector::init()->setQuery($item->title)
                                                    ->setType(YandexConnector::BIZ_TYPE)
                                                    ->makeRequest());

                $rubricName = array_get($getRubric, 'features.0.properties.CompanyMetaData.Categories.0.name');

                if ($rubricName) {
                    $firmList      = static::prepareData(Firms::find($rubricName, $polygon)->getItems());
                    $firm          = array_get($firmList, '1') ?: array_get($firmList, '0');
                    $addressName   = array_get($firm, 'address_name', '');
                    $firmName      = array_get($firm, 'name', '');
                    $point         = array_get($firm, 'point', []);
                    $item->lon     = array_get($point, 'lon', null);
                    $item->lat     = array_get($point, 'lat', null);
                    $item->address = sprintf('%s (%s)', $firmName, $addressName);
                }
            }

            return $itemsWithoutPoint;
        } catch (GisRequestException $e) {
            throw $e;
        }
    }

    /**
     * @param mixed $items
     *
     * @return array
     */
    public static function prepareData($items)
    {
        if (is_object($items)) {
            $items = (array)$items;
        }

        if (is_array($items)) {
            $new = [];

            foreach ($items as $key => $val) {
                $new[$key] = static::prepareData($val);
            }
        } else $new = $items;

        return $new;
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
                $wayCost += (float)static::getEdgeCost($listItems[$way[$j]], $listItems[$way[$j + 1]]);
            }
            if ($wayCost < $cost) {
                $cost    = $wayCost;
                $initial = $way;
            }
        }

        $out = [];
        for ($j = 0; $j < count($way); $j++) {
            $out[] = $listItems[$initial[$j]];
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

    protected static function getEdgeCost($point1, $point2)
    {
        $cacheKey = static::getEdgeCacheKey($point1, $point2);

        if (!($edgeCost = Cache::get($cacheKey))) {
            if ($point1->lon != $point2->lon || $point1->lat != $point2->lat) {
                $routePoints = new CarRouteParams();
                $routePoints->addWaypoint([$point1->lon, $point1->lat]);
                $routePoints->addWaypoint([$point2->lon, $point2->lat]);
                Api2Gis::call()->CarRouteDirectionsAsync($routePoints);
                $edge     = Api2Gis::call()->execute();
                $edgeCost = $edge[0]->items[0]->total_distance;
            } else {
                $edgeCost = 0.0000001;
            }
            Cache::put($cacheKey, $edgeCost, 30);
        }
        $test = 0;

        return $edgeCost;
    }

    /**
     * @param $point1
     * @param $point2
     *
     * @return string
     */
    protected static function getEdgeCacheKey($point1, $point2)
    {
        $key = sprintf('edge_%s_%s_to_%s_%s', $point1->lon, $point1->lat, $point2->lon, $point2->lat);

        return $key;
    }
}