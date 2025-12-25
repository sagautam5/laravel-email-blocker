<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Sagautam5\EmailBlocker\Abstracts\BaseMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class ReceiverTypeDistributionMetric extends BaseMetric
{
    public function getName(): string
    {
        return 'Receiver Type Distribution';
    }

    /**
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    public function calculate(array $filters = []): array
    {
        $query = $this->applyDateFilters($this->getQuery(), $filters);

        return $query
            ->get()
            ->toArray();
    }

    /**
     * @return Builder<BlockedEmail>
     */
    protected function getQuery(): Builder
    {
        // @phpstan-ignore-next-line
        return BlockedEmail::query()
            ->select('receiver_type', DB::raw('COUNT(*) as total'))
            ->groupBy('receiver_type')
            ->orderByDesc('total');
    }
}
