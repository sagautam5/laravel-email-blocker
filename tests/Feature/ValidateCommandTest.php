<?php

it('passes when configuration is valid', function () {
    $this->artisan('email-blocker:validate')
        ->expectsOutput('✔ Email Blocker configuration is valid.')
        ->assertExitCode(0);
});

it('fails when configuration is invalid', function () {
    config()->set('email-blocker.settings.global_block', 'invalid');

    $this->artisan('email-blocker:validate')
        ->expectsOutput('✖ Email Blocker configuration is invalid.')
        ->assertExitCode(1);
});

it('outputs validation error message', function () {
    config()->set('email-blocker.settings.global_block', 'invalid');

    $this->artisan('email-blocker:validate')
        ->expectsOutputToContain('settings.global_block')
        ->assertExitCode(1);
});
