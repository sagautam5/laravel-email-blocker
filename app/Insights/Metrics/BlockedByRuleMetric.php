<?php 

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Sagautam5\EmailBlocker\Models\BlockedEmail;
use Illuminate\Support\Facades\DB;
use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Illuminate\Database\Eloquent\Builder;

class BlockedByRuleMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Blocked Emails by Rule';
    }

    public function calculate(array $filters = []): array
    {
        $query = $this->applyDateFilters($this->getQuery(), $filters);

        return $query->get()->toArray();
    }

    protected function getQuery(): Builder
    {
        return BlockedEmail::query()
            ->select('rule', DB::raw('COUNT(*) as total'))
            ->groupBy('rule')
            ->orderByDesc('total');
    }
}
