<?php

namespace Sagautam5\EmailBlocker\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Sagautam5\EmailBlocker\Contracts\Metric;

abstract class AbstractMetric implements Metric
{
    /**
     * Optional: common filters like date range
     */
    protected array $filters = [];

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    protected function applyDateFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['start_date'])) {
            $query->where('blocked_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->where('blocked_at', '<=', $filters['end_date']);
        }

        return $query;
    }

    /**
     * AbstractMetric forces implementing calculate()
     */
    abstract public function calculate(array $filters = []): array;
}
