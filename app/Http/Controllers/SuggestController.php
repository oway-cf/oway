<?php

namespace App\Http\Controllers;

use akeinhell\Api2Gis;
use akeinhell\Exceptions\GisRequestException;
use akeinhell\RequestParams\BranchParams;
use akeinhell\RequestParams\RubricParams;
use akeinhell\RequestParams\SearchParams;
use akeinhell\Types\GeoType;
use App\Http\Requests;
use App\Models\Firms;
use Mockery\CountValidator\Exception;

class SuggestController extends Controller
{
    public function address($query = null)
    {
        $query = $query ?: request('query');
        if (!$query) {
            return response('query not defined', 404);
        }

        $searchParams = new SearchParams();
        $searchParams->setType([
            GeoType::building,
            GeoType::street,
        ]);
        $searchParams
            ->setQuery($query)
            ->setFields([
                'items.geometry.selection'
            ])
            ->setRegionId(request('region', 1))
            ->setPageSize(5);
        try {
            $data = Api2Gis::call()->search($searchParams);
        } catch (GisRequestException $e) {
            $data = null;
        }

        $addressList = [];

        if ($data) {
            foreach ($data->getItems() as $item) {
                $location = null;
                $lat      = null;
                $lon      = null;
                if (preg_match('/point/i', $item->geometry->selection)) {
                    $location = preg_replace('/point\((.*?)\)/i', '$1', $item->geometry->selection);
                    list($lon, $lat) = explode(' ', $location);
                }
                if (!$location) {
                    continue;
                }
                $addressList[] = [
                    "key"      => $item->id,
                    "title"    => $item->full_name,
                    'type'     => 'address',
                    "address"  => $item->name,
                    "location" => [
                        'lon' => $lon,
                        'lat' => $lat,
                    ]
                ];
            }
        }
        $companyList   = [];
        $companyParams = new BranchParams();
        $companyParams
            ->setQuery($query)
            ->setFields([
                'items.point'
            ])
            ->setRegionId(request('region', 1))
            ->setPageSize(5);

        try {
            $data = Api2Gis::call()->BranchSearch($companyParams);
        } catch (GisRequestException $e) {
            $data = null;
        }

        if ($data) {
            foreach ($data->getItems() as $item) {
                if (!$item->point) {
                    continue;
                }
                $companyList[] = [
                    "key"      => $item->id,
                    'type'     => 'company',
                    "title"    => $item->name,
                    "address"  => $item->address_name,
                    "location" => [
                        'lon' => $item->point->lon,
                        'lat' => $item->point->lat,
                    ]
                ];
            }
        }

        $userQueryList = [
            [
                "key"      => null,
                "title"    => $query,
                'type'     => 'rubric',
                "address"  => null,
                "location" => [
                    'lon' => null,
                    'lat' => null,
                ]
            ]
        ];

        $return = array_merge($userQueryList, $addressList, $companyList);

        return response()->json($return);
    }
}
