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

        try {
            $request->merge([
                'url' => preg_replace('/^https?:\/\//', '', $request->url), // remove http:// or https://
            ]);
            // 1. Validate incoming data
            $validated = $request->validate([
                'title'      => 'required|string|max:255',
                'url' => ['required', 'regex:/^([\w.-]+\.[a-z]{2,})(\/.*)?$/i', 'max:255'],
                'alias'      => 'required|string|max:255|unique:stocks,alias',
                'metaFields' => 'required|array'
            ]);

        // 2. Create new stock
        $stock = Stock::create([
            'name'      => $validated['title'],
            'url'        => $validated['url'],
            'alias'      => $validated['alias'],
            'metaFields' => json_encode($validated['metaFields']),
        ]);

        // 3. Return success response
        return response()->json([
            'message'   => 'New stock added',
            'newStock'  => $stock,
        ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // Any other errors
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(), // remove in production if sensitive
            ], 500);
    }


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
    public function update(Request $request, $id)
    {

        
        $request->merge([
            'url' => preg_replace('/^https?:\/\//', '', $request->url), // remove http:// or https://
        ]);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'url' => ['required', 'regex:/^([\w.-]+\.[a-z]{2,})(\/.*)?$/i', 'max:255'],
            'alias'      => 'required|string|max:255|unique:stocks,alias,' . $id,
            'metaFields' => 'required|array', 
        ]);

        try {

        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json([
                'message' => 'Stock not found',
            ], 404);
        }

        $validated['metaFields'] = json_encode($validated['metaFields']);

        $validated['name'] = $validated['title'];
        unset($validated['title']);

        $stock->update($validated);

        return response()->json([
            'message' => 'Stock updated successfully',
            'stock'   => $stock,
        ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while updating Stock',
                'error'   => $e->getMessage(), // optional for debugging
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        
        try {

            $deleted = Stock::where('id', $id)->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Stock Deleted!',
            ], 200);
        }
            return response()->json([
                'message' => 'Stock not found',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while deleting record',
                'error'   => $e->getMessage(), // optional for debugging
            ], 500);
        }
        
    }

}
