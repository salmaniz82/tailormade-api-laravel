<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        /*
        'metaFields' => 'array'
        */
    ];

    public static function getStocks(){

        $stocks = Stock::select('id', 'name', 'url', 'metaFields')->get()->toArray();
        $all = [
            "id"    => 1,
            "name"   => "All",
            "url"    => "all",
            "active" => true,
        ];
        array_unshift($stocks, $all);

        return $stocks;

    }

    public static function swatchMeta(){


        /*
        $stocks = Stock::select('id', 'name', 'url', 'alias', 'metaFields')->get()->toArray();
        */

        $stocks = Stock::select('id', 'name as title', 'url', 'alias', 'metaFields')
        ->get()
        ->toArray();
        
        return $stocks;


    }

}
