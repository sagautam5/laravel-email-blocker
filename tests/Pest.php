<?php

use Sagautam5\EmailBlocker\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function nextClosure(): Closure {
    return fn (array $emails) => $emails;
}