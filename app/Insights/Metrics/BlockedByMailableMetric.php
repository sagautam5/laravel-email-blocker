<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class BlockedByMailableMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Blocked Emails by Mailable';
    }

    public function calculate(array $filters = []): array
    {
        $query = $this->applyDateFilters($this->getQuery(), $filters);

        return $query->get()->toArray();
    }

    protected function getQuery(): Builder
    {
        return BlockedEmail::query()
            ->whereNotNull('mailable')
            ->select('mailable', DB::raw('COUNT(*) as total'))
            ->groupBy('mailable')
            ->orderByDesc('total');
    }
}
