<?php

namespace App\Services;

use \Illuminate\Support\Str;

class OrderByService
{
    public function applyRelationshipOrderBy($query, $orderBy, $orderDirection, $table)
    {
        $splitted = Str::of($orderBy)->explode('.');
        $firstItem = $splitted[0];
        $model = "\\App\Models\\" . Str::studly($firstItem);
        $column = $splitted[1];

        $query->orderBy(
            $model::select($column)
                ->from(Str::plural($firstItem))
                ->whereColumn(
                    Str::plural($firstItem) . ".id",
                    $table . '.' . $firstItem . '_id'
                ),
            $orderDirection
        );
    }
}
