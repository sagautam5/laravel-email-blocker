<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class TopMailableRulePairsMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Top Mailable Rule Pairs';
    }

    /**
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    public function calculate(array $filters = []): array
    {
        return $this->applyDateFilters($this->getQuery(), $filters)
            ->limit((int) ($filters['limit'] ?? 10))
            ->get()
            ->map(fn ($row) => [
                'mailable' => $row->mailable ?? '',
                'rule'     => $row->rule ?? '',
                'count'    => (int) ($row->total ?? 0),
            ])
            ->toArray();
    }

    /**
     * @return Builder<BlockedEmail>
     */
    protected function getQuery(): Builder
    {
        // @phpstan-ignore-next-line
        return BlockedEmail::query()
            ->select([
                DB::raw("COALESCE(mailable, 'Unknown') as mailable"),
                DB::raw("COALESCE(rule, 'Unknown') as rule"),
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('mailable', 'rule')
            ->orderByDesc('total');
    }
}
