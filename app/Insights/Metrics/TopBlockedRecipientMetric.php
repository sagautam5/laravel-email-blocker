<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Sagautam5\EmailBlocker\Abstracts\BaseMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class TopBlockedRecipientMetric extends BaseMetric
{
    public function getName(): string
    {
        return 'Top Blocked Recipients';
    }

    /**
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    public function calculate(array $filters = []): array
    {
        $query = $this->applyDateFilters($this->getQuery(), $filters);

        $query = $this->applyDateFilters($query, $filters);

        $limit = (int) ($filters['limit'] ?? 10);

        return $query->take($limit)->get()->toArray();
    }

    /**
     * @return Builder<BlockedEmail>
     */
    protected function getQuery(): Builder
    {
        // @phpstan-ignore-next-line
        return BlockedEmail::query()
            ->select('email', DB::raw('COUNT(*) as total'))
            ->groupBy('email')
            ->orderByDesc('total');
    }
}
