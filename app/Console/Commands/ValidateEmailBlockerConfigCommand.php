<?php

namespace Sagautam5\EmailBlocker\Console\Commands;

use Illuminate\Console\Command;
use Sagautam5\EmailBlocker\Exceptions\InvalidConfigurationException;
use Sagautam5\EmailBlocker\Validators\EmailBlockerConfigValidator;

class ValidateEmailBlockerConfigCommand extends Command
{
    protected $signature = 'email-blocker:validate';

    protected $description = 'Validate the Email Blocker configuration';

    public function handle(): int
    {
        try {
            EmailBlockerConfigValidator::validate(
                config('email-blocker')
            );

            $this->info('✔ Email Blocker configuration is valid.');

            return self::SUCCESS;
        } catch (InvalidConfigurationException $e) {
            $this->error('✖ Email Blocker configuration is invalid.');
            $this->line('');
            $this->line($e->getMessage());

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('✖ Unexpected error while validating configuration.');
            $this->line($e->getMessage());

            return self::FAILURE;
        }
    }
}
