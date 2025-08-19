<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Swatch extends Model
{

    protected $guarded = [];

    protected static $filterKeys = ['Mill', 'Bunch', 'Season', 'Garment Type'];


    public static function getDynamicFilters(array $filterKeys = null): array
    {
        $filterKeys = $filterKeys ?? self::$filterKeys;

        return Cache::rememberForever('swatch_dynamic_filters', function () use ($filterKeys) {
            return self::buildDynamicFilters($filterKeys);
        });
    }

    /**
     * Rebuild filters and update cache (call this after create/update/delete).
     */
    public static function refreshDynamicFilters(array $filterKeys = null): array
    {
        $filterKeys = $filterKeys ?? self::$filterKeys;

        $filters = self::buildDynamicFilters($filterKeys);

        Cache::forever('swatch_dynamic_filters', $filters);

        return $filters;
    }

    /**
     * Internal: actually query DB for unique values.
     */
    private static function buildDynamicFilters(array $filterKeys): array
    {
        $filters = [];

        foreach ($filterKeys as $key) {
            $values = DB::table('swatches')
                ->where('status', 1)
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(productMeta, '$.\"$key\"')) as value")
                ->pluck('value')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (!empty($values)) {
                $filters[] = [
                    'name'  => $key,
                    'items' => $values,
                ];
            }
        }

        return $filters;
    }

}
