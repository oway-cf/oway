<?php

namespace App\Models;

class Graph
{
    /**
     * @param TodoListItem[] $listItems
     * @return TodoListItem[]
     */
    public static function calculateItemsPoints(array $listItems = [])
    {
        return $listItems;
        return [
            [
                'title' => 'Julius Meinl',
                'address' => 'Линейная, 37/2',
                'lon' => "82.910206",
                'lat' => "55.04982",
            ],
            [
                'title' => 'Кофейные технологии, ООО, торгово-сервисная компания',
                'address' => 'Писарева, 60',
                'lon' => "82.903137",
                'lat' => "55.060355",
            ],
            [
                'title' => 'Колибри, ООО, торговая компания',
                'address' => 'Дачная, 21/1',
                'lon' => "82.954886",
                'lat' => "55.013059",
            ],
        ];
    }
} 