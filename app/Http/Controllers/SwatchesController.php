<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use \App\Models\Swatch;
use \App\Models\Stock;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SwatchesController extends Controller
{
    /*
        - Display a listing of the resource.
    : JsonResponse
     */

    public function index(Request $request)
    {
        try {
            $page    = (int) $request->query('page', 1);
            $limit   = (int) $request->query('limit', 12);
            $status  = $request->query('status', 1);
            $source  = $request->query('source', 'all');
            $filteringActivate = $request->query('filteringActivate', 'off');
            
            // Parse raw query string to handle parameters with spaces
            $rawQueryString = $request->getQueryString();
            $customParams = [];
            if ($rawQueryString) {
                // Manually parse the query string to handle spaces in parameter names
                $pairs = explode('&', $rawQueryString);
                foreach ($pairs as $pair) {
                    if (strpos($pair, '=') !== false) {
                        list($key, $value) = explode('=', $pair, 2);
                        $key = urldecode($key);
                        $value = urldecode($value);
                        $customParams[$key] = $value;
                    }
                }
            }
            
            $query = Swatch::query()->where('status', $status);
            
            if ($source !== 'all') {
                $query->where('source', $source);
            }

            $allowedFilterKeys = ['Mill', 'Bunch', 'Season', 'Garment Type'];
            $appliedFilters = [];

            if ($filteringActivate === 'on') {
                foreach ($allowedFilterKeys as $key) {
                    // Try to get parameter value from custom parsed params first
                    $paramValue = $customParams[$key] ?? null;
                    
                    if (!$paramValue) {
                        // Fallback to Laravel's query method
                        $paramValue = $request->query($key);
                    }
                    
                    if ($paramValue && $paramValue !== '') {
                        $values = array_map('trim', explode(',', $paramValue));
                        
                        // Filter out empty values
                        $values = array_filter($values, function($value) {
                            return !empty($value) && $value !== '';
                        });
                        
                        if (!empty($values)) {
                            // Save applied filters
                            $appliedFilters[$key] = $values;

                            // Use the proven approach with IN clause
                            $quotedTermsArray = array_map(function($term) {
                                return "'" . addslashes($term) . "'";
                            }, $values);
                            
                            $formattedTermString = implode(',', $quotedTermsArray);
                            $jsonPath = '$."' . $key . '"';
                            
                            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$jsonPath')) IN ($formattedTermString)");
                        }
                    }
                }
            }

            $total = $query->count();
            $swatches = $query->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();
            $filters = Swatch::getDynamicFilters();
            $stocks = Stock::getStocks();

            return response()->json([
                'collections' => $swatches,
                'filters' => $filters,   // useful for frontend
                'sources' => $stocks,
                'applied_filters' => $appliedFilters, // for debugging
                'meta' => [
                    'page'   => $page,
                    'limit'  => $limit,
                    'total'  => $total,
                    'pages'  => ceil($total / $limit),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Swatches API Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'An error occurred while fetching swatches',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getSourceFilterStaticKeys(string $source, bool $clean = false): array
    {
        if ($source === 'all') {
            $keys = ['Mill', 'Bunch', 'Description', 'Composition', 'Weight', 'Season', 'Garment Type'];
        } else {
            $keys = $this->dynamicDBFilters($source, $clean);
        }

        return $clean ? $keys : $this->replaceSpacesWithUnderscores($keys);
    }

    private function replaceSpacesWithUnderscores(array $array): array
    {
        return array_map(fn($item) => str_replace(' ', '_', $item), $array);
    }

    private function dynamicDBFilters(string $source, bool $clean = false): array
    {
        $stock = Stock::where('url', $source)->first();
        if (!$stock || !$stock->metaFields) {
            return [];
        }

        return json_decode($stock->metaFields, true) ?? [];
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(string $id)
    {
        /*
        dd(request());
        $swatches = Swatch::all();
        $swatches = Swatch::where('source', 'harrisons1863.com')->get();
        return response()->json([
                'collection' => $swatches,
                'message' => 'Admin user already exists',
            ], 200); // Conflict
        */
    }
}



