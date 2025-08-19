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
       

    $paramsQuery = [];

    $paramsQuery['page'] = $request->query('page', 1);   
    $paramsQuery['limit'] = $request->query('limit', 12); 
    $paramsQuery['offset'] = ($paramsQuery['page'] - 1) * $paramsQuery['limit'];

    $paramsQuery['filteringActivate'] = $request->query('filteringActivate', 'off');
    $paramsQuery['source'] = $request->query('source', 'all');
    $paramsQuery['status'] = $request->query('status', 1);

    // Special handling for multiple filters (comma or + separated)
    $paramsQuery['Mill'] = $this->parseFilter($request->query('Mill'));
    $paramsQuery['Bunch'] = $this->parseFilter($request->query('Bunch'));
    $paramsQuery['Season'] = $this->parseFilter($request->query('Season'));
    $paramsQuery['GarmentType'] = $this->parseFilter($request->query('Garment Type'));


        $stocks = Stock::getStocks();
        return response()->json($stocks, 200);
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
