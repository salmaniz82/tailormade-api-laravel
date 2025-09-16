<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;

use \App\Models\Swatch;
use \App\Models\Stock;
 
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

            Log::error('rawQuery String: ', [
                'query_string' => $rawQueryString
            ]);


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
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'status'      => 'required|boolean',
            'source'      => 'required|string|exists:stocks,source',
            'productMeta' => 'required|string', // will store as raw JSON string
            'file'        => 'required|file|image|max:2048', // 2MB limit
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // ✅ Find alias from stock
            $stock = Stock::where('source', $request->source)->first();
            if (!$stock) {
                return response()->json([
                    'message' => 'Invalid stock source',
                ], 404);
            }
            $alias = $stock->alias;

            // ✅ File handling
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

            $aliasRootDirectory      = public_path("uploads/images/{$alias}/");
            $aliasOriginalDirectory  = public_path("uploads/images/{$alias}/original/");
            $aliasThumbnailDirectory = public_path("uploads/images/{$alias}/thumbnail/");

            foreach ([$aliasRootDirectory, $aliasOriginalDirectory, $aliasThumbnailDirectory] as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
            }

            $rootPath      = $aliasRootDirectory . $fileName;
            $originalPath  = $aliasOriginalDirectory . $fileName;
            $thumbnailPath = $aliasThumbnailDirectory . $fileName;

            // ✅ Move original file
            $file->move($aliasRootDirectory, $fileName);

            // ✅ Make thumbnail
            $imageManager = new ImageManager(['driver' => 'gd']);
            $image = $imageManager->make($rootPath)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $image->save($thumbnailPath);

            // ✅ Save in DB
            $swatch = Swatch::create([
                'title'        => $request->title,
                'source'       => $request->source,
                'status'       => $request->status,
                'productMeta'  => $request->productMeta,
                'imageUrl'     => "/uploads/images/{$alias}/{$fileName}",
                'thumbnail'    => "/uploads/images/{$alias}/thumbnail/{$fileName}",
                'productPrice' => 'n/a',
            ]);

            // ✅ Refresh cached filters
            Swatch::refreshDynamicFilters();

            return response()->json([
                'message' => 'Swatch added successfully',
                'swatch'  => $swatch,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error while adding swatch',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        try {

            $swatch = Swatch::select('id', 'title', 'imageUrl', 'thumbnail', 'productPrice', 'productMeta', 'source', 'status')
                ->find($id);
            if ($swatch) {
                return response()->json([
                    'message' => 'Fetch success',
                    'swatch'  => $swatch->toArray(),
                ], 200);
            }
            // Not found
            return response()->json([
                'message' => 'Swatch not found',
            ], 404);

        } catch (\Exception $e) {
            
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(), // ⚠️ remove in production
            ], 500);
        }
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

    public function update(Request $request, $id)
    {
        if ($request->operation === 'status-toggle') {
            return $this->statusToggle($request, $id);
        }

        return $this->contentUpdate($request, $id);
    }

    private function statusToggle(Request $request, $id) {

    $request->validate([
        'status' => 'required|boolean',
    ]);

    $swatch = Swatch::find($id);

    if (!$swatch) {
        return response()->json([
            'message' => 'Swatch not found',
            'operation-status' => 'failed'
        ], 404);
    }

    try {
        $swatch->status = $request->status ? 1 : 0;
        $swatch->save();

        Swatch::refreshDynamicFilters();

        return response()->json([
            'message' => 'Status updated',
            'operation-status' => 'ok'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error while updating status',
            'operation-status' => 'failed',
            'debug' => $e->getMessage()
        ], 500);
    }
}

private function contentUpdate(Request $request, $id)
{
    $swatch = Swatch::find($id);

    if (!$swatch) {
        return response()->json([
            'message' => 'Swatch not found',
            'operation-status' => 'failed'
        ], 404);
    }

    $validated = $request->validate([
        'title'       => 'sometimes|required|string|max:255',
        'source'      => 'sometimes|required|string|max:255',
        'productMeta' => 'sometimes|required|json',
        'status'      => 'sometimes|required|boolean',
        // add more fields as needed
    ]);

    try {
        $swatch->update($validated);

        Swatch::refreshDynamicFilters();

        return response()->json([
            'message' => 'Swatch updated successfully',
            'operation-status' => 'ok'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error while updating swatch',
            'operation-status' => 'failed',
            'debug' => $e->getMessage()
        ], 500);
    }
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