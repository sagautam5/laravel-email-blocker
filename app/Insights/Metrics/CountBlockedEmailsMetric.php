<?php

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;
use Illuminate\Database\Eloquent\Builder;

class CountBlockedEmailsMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Total Blocked Emails';
    }

    /**
     * @param  array<string>  $filters
     * @return array<mixed>
     */
    public function calculate(array $filters = []): array
    {
        /**
         * @var Builder<BlockedEmail> $query
         */
        $query = BlockedEmail::query();

        $query = $this->applyDateFilters($query, $filters);

        return [
            'count' => $query->count(),
        ];
    }
}
