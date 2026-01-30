<?php

namespace App\Helpers;
use Illuminate\Database\Eloquent\Builder;
trait Sortable
{
    public function scopeApplySorting(Builder $query, array $filters): Builder
    {
        $sortBy = $filters['sort_by'] ?? 'id';
        $direction = strtolower($filters['sort_direction'] ?? 'asc');

        if (!in_array($sortBy, $this->sortableColumns ?? [])) {
            $sortBy = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        return $query->orderBy($sortBy, $direction);
    }
}
