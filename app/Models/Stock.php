<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'metaFields' => 'array'
    ];

    public static function getStocks(){

        $stocks = Stock::select('id', 'name', 'url', 'active')->get()->toArray();
        $all = [
            "id"    => 1,
            "name"   => "All",
            "url"    => "all",
            "active" => true,
        ];
        array_unshift($stocks, $all);

        return $stocks;

    }

}
