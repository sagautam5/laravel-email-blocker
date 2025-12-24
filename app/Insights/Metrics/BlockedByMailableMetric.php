<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class BlockedByMailableMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Blocked Emails by Mailable';
    }

    /**
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    public function calculate(array $filters = []): array
    {
        $query = $this->applyDateFilters($this->getQuery(), $filters);

        return $query
            ->limit((int) ($filters['limit'] ?? 10))
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
            ->whereNotNull('mailable')
            ->select('mailable')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('mailable')
            ->orderByDesc('total');
    }
}
