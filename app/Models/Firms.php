<?php

namespace App\Models;

use akeinhell\Api2Gis;
use akeinhell\RequestParams\BranchParams;

/**
 * Class Firms
 * @package App\Models
 */
class Firms
{
    /**
     * @param string $query
     * @param string $polygon
     *
     * @return \akeinhell\Types\GisResponse
     */
    public static function find($query, $polygon) {
        $firmParams = new BranchParams();
        $firmParams->setQuery($query)
            ->setRegionId(1)
            ->setFields(['items.point'])
            ->setSort('flamp_rating')
            ->setPageSize(2)
            ->setPoligon($polygon);

        return Api2Gis::call()->BranchSearch($firmParams);
    }

}