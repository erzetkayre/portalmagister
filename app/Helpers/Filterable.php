<?php

namespace App\Helpers;
use Illuminate\Database\Eloquent\Builder;
trait Filterable
{
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        if (empty($this->filterableColumns)) {
            return $query;
        }

        foreach ($this->filterableColumns as $key => $columns) {
            $value = $filters[$key] ?? null;

            // Skip jika value kosong
            if (empty($value)) {
                continue;
            }

            // Search (LIKE) - multiple columns
            if (is_array($columns)) {
                $query->where(function ($q) use ($columns, $value) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', "%{$value}%");
                    }
                });
            }
            // Exact match - single column
            else {
                $query->where($columns, $value);
            }
        }

        return $query;
    }
}
