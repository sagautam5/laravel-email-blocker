<?php 

namespace Sagautam5\EmailBlocker\Insights\Metrics;

use Sagautam5\EmailBlocker\Abstracts\AbstractMetric;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

class CountBlockedEmailsMetric extends AbstractMetric
{
    public function getName(): string
    {
        return 'Total Blocked Emails';
    }

    public function calculate(array $filters = []): array
    {
        $query = BlockedEmail::query();

        $query = $this->applyDateFilters($query, $filters);

        return [
            'count' => $query->count(),
        ];
    }
}
