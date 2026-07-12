<?php

namespace App\Http\Responses;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function paginated(LengthAwarePaginator $paginator, string $resourceClass): array
    {
        $paginator->appends(request()->query());

        $items = collect($paginator->items())->map(
            fn($item) => (new $resourceClass($item))->resolve(request())
        );

        return [
            'page' => $paginator->currentPage(),
            'page_size' => $paginator->perPage(),
            'next' => $paginator->nextPageUrl(),
            'previous' => $paginator->previousPageUrl(),
            'count' => $paginator->total(),
            'total_page' => $paginator->lastPage(),
            'data' => $items,
        ];
    }

    protected function perPage(?int $default = 15): int
    {
        return min((int) request()->input('page_size', $default), 100);
    }

    protected function applySearch(Builder $query, array $fields): Builder
    {
        $search = request('search');

        if ($search) {
            $query->where(function (Builder $q) use ($fields, $search) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }

    protected function applySort(Builder $query): Builder
    {
        $sort = request('sort');

        if (!$sort) {
            return $query;
        }

        $sorts = explode(',', $sort);

        foreach ($sorts as $sortField) {
            $direction = 'asc';

            if (str_starts_with($sortField, '-')) {
                $direction = 'desc';
                $sortField = substr($sortField, 1);
            }

            if (str_contains($sortField, '.')) {
                [$relation, $column] = explode('.', $sortField, 2);

                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $relation) ||
                    !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                    continue;
                }

                try {
                    $rel = $query->getModel()->{$relation}();

                    if ($rel instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                        $query->orderBy(
                            $rel->getRelated()->newQuery()
                                ->select($column)
                                ->whereColumn(
                                    $rel->getQualifiedOwnerKeyName(),
                                    '=',
                                    $rel->getQualifiedForeignKeyName()
                                )
                                ->limit(1),
                            $direction
                        );
                    }
                } catch (\Exception) {
                    continue;
                }
            } elseif (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $sortField)) {
                $query->orderBy($sortField, $direction);
            }
        }

        return $query;
    }

    protected function applyFilters(Builder $query): Builder
    {
        $filters = request('filter');

        if (!$filters) {
            return $query;
        }

        $filters = is_array($filters) ? $filters : [$filters];

        foreach ($filters as $filter) {
            $parts = explode(';', $filter);

            if (count($parts) < 3) {
                continue;
            }

            [$field, $operator, $value] = $parts;

            if (str_contains($field, '.')) {
                [$relation, $column] = explode('.', $field, 2);

                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $relation) ||
                    !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                    continue;
                }

                try {
                    match ($operator) {
                        'eq' => $query->whereHas($relation, fn($q) => $q->where($column, $value)),
                        'neq' => $query->whereHas($relation, fn($q) => $q->where($column, '!=', $value)),
                        'contains' => $query->whereHas($relation, fn($q) => $q->where($column, 'like', "%{$value}%")),
                        'gt' => $query->whereHas($relation, fn($q) => $q->where($column, '>', $value)),
                        'gte' => $query->whereHas($relation, fn($q) => $q->where($column, '>=', $value)),
                        'lt' => $query->whereHas($relation, fn($q) => $q->where($column, '<', $value)),
                        'lte' => $query->whereHas($relation, fn($q) => $q->where($column, '<=', $value)),
                        'in' => $query->whereHas($relation, fn($q) => $q->whereIn($column, explode(',', $value))),
                        'between' => $query->whereHas($relation, fn($q) => $q->whereBetween($column, array_slice(explode(',', $value), 0, 2))),
                        default => null,
                    };
                } catch (RelationNotFoundException) {
                    continue;
                }
            } else {
                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $field)) {
                    continue;
                }

                match ($operator) {
                    'eq' => $query->where($field, $value),
                    'neq' => $query->where($field, '!=', $value),
                    'contains' => $query->where($field, 'like', "%{$value}%"),
                    'gt' => $query->where($field, '>', $value),
                    'gte' => $query->where($field, '>=', $value),
                    'lt' => $query->where($field, '<', $value),
                    'lte' => $query->where($field, '<=', $value),
                    'in' => $query->whereIn($field, explode(',', $value)),
                    'between' => $query->whereBetween($field, array_slice(explode(',', $value), 0, 2)),
                    default => null,
                };
            }
        }

        return $query;
    }
}
