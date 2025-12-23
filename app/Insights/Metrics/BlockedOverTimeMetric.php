<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class BlockedOverTimeMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Blocked Emails Over Time';
    }

    /**
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    public function calculate(array $filters = []): array
    {
        $query = $this->applyDateFilters($this->getQuery(), $filters);

        return $query->get()->toArray();
    }

    /**
     * @return Builder<BlockedEmail>
     */
    protected function getQuery(): Builder
    {
        // @phpstan-ignore-next-line
        return BlockedEmail::query()
            ->select(DB::raw('DATE(blocked_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(blocked_at)'))
            ->orderBy('date');
    }
}
