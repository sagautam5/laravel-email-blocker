<?php

namespace Sagautam5\EmailBlocker\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Sagautam5\EmailBlocker\Contracts\Metric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

abstract class BaseMetric implements Metric
{
    /**
     * Optional: common filters like date range
     *
     * @var array<string>
     */
    protected array $filters = [];

    /**
     * @param  array<string>  $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @param  Builder<BlockedEmail>  $query
     * @param  array<string>  $filters
     * @return Builder<BlockedEmail>
     */
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
     *
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    abstract public function calculate(array $filters = []): array;
}
