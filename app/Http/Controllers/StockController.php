<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        try {

            $stocks  = Stock::getStocks();
            array_shift($stocks);          
            $data['stocks'] = $stocks;
            return response()->json($data, 200);
        }
        catch(\Exception $e) {  
            $data["message"] = "Failed while fetching swatch meta data";
            return response()->json($data, 500);
        }
    }

    public function swatchMeta(Request $request) 
    {

        try {
            $data['metadata'] = Stock::swatchMeta();
            $data['message'] = "Success";
            return response()->json($data, 200);
        }
        catch(\Exception $e) {  
            $data["message"] = "Failed while fetching swatch meta data";
            return response()->json($data, 500);
        }

    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        //
    }
}
