<?php

namespace Sagautam5\EmailBlocker\Contracts;

interface Metric
{
    /**
     * Returns metric name/description
     */
    public function getName(): string;

    /**
     * Generates the metric data
     *
     * @param  array<string>  $filters
     * @return array<string>
     */
    public function calculate(array $filters = []): array;
}
