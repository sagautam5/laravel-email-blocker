<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class TopBlockedSenderMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Top Blocked Senders';
    }

    public function calculate(array $filters = []): array
    {
        $query = $this->applyDateFilters($this->getQuery(), $filters);

        $limit = $filters['limit'] ?? 10;

        return $query->take($limit)->get()->toArray();
    }

    protected function getQuery(): Builder
    {
        return BlockedEmail::query()
            ->select('from_email', DB::raw('COUNT(*) as total'))
            ->groupBy('from_email')
            ->orderByDesc('total');
    }
}
