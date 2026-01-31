<?php

namespace App\Helpers;
use Illuminate\Database\Eloquent\Builder;
trait Sortable
{
    public function scopeApplySorting(Builder $query, array $filters): Builder
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $direction = $filters['sort_direction'] ?? 'asc';

        if (!in_array($sortBy, $this->sortableColumns ?? [])) {
            $sortBy = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        return $query->orderBy($sortBy, $direction);
    }
}
