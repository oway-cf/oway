<?php

namespace App\Http\Controllers;

use akeinhell\Api2Gis;
use akeinhell\Exceptions\GisRequestException;
use akeinhell\RequestParams\BranchParams;
use akeinhell\RequestParams\RubricParams;
use akeinhell\RequestParams\SearchParams;
use akeinhell\Types\GeoType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;

class SuggestController extends Controller
{

    public function address($json = true)
    {
        $query = request('query');
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
                $lat = $lon = null;
                if (preg_match('/point/i', $item->geometry->selection)) {
                    $location = preg_replace('/point\((.*?)\)/i', '$1', $item->geometry->selection);
                    list($lat, $lon) = explode(' ', $location);
                }
                if (!$location) {
                    continue;
                }
                $addressList[] = [
                    "title" => $item->full_name,
                    'type' => 'address',
                    "address" => $item->name,
                    "location" => [
                        'lat' => $lat,
                        'lon' => $lon
                    ]
                ];
            }
        }
        $companyList = [];
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
                    'type' => 'company',
                    "title" => $item->name,
                    "address" => $item->address_name,
                    "location" => [
                        'lon' => $item->point->lon,
                        'lat' => $item->point->lat,
                    ]
                ];
            }
        }
        $return = array_merge($addressList, $companyList);
        return $json ? response()->json($return) : $return;
    }


    /**
     * @todo рубрика
     * @todo фирма
     * @todo достопримечательность
     * @param bool $json
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function keyword($json = true)
    {
        throw new Exception('Не реализовано!!! :-)');
        return response()->json([]);
        $query = request('query');
        $company = [];
        $rubric = [];


        $params = new RubricParams();
        $params
            ->setQuery($query)
            ->setRegionId(request('region', 1))
            ->setPageSize(5);

        try {
            $data = Api2Gis::call()->RubricSearch($params);
        } catch (GisRequestException $e) {
            $data = null;
        }

        if ($data) {
            foreach ($data->getItems() as $item) {
                $company[] = [
                    'key' => $item->id,
                    'type' => 'rubric',
                    "title" => $item->name,
                    'address' => null,
                    'rubric' => null,
                    'location' => null,
                ];
            }
        }


        $result = array_merge($rubric, $company);
        return $json ? response()->json($result) : $result;
    }

    public function index()
    {
        $data = array_merge(
            $this->address(false),
            $this->keyword(false)

        );
        return response()->json($data);
    }
}
