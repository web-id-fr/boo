<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BackupAttempts extends Command
{
    protected $signature = 'backup:run-attempts {--attempts=3 : Number of attempts} {--filename=} {--only-db} {--db-name=*} {--only-files} {--only-to-disk=} {--disable-notifications} {--timeout=}';

    protected $description = 'Run the backup and retry if it fails.';

    public function handle(): int
    {
        $attempts = $this->option('attempts');

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            $this->info("Attempt {$attempt}/{$attempts}");

            $options = $this->input->getOptions();
            unset($options['attempts']);
            $optionDefaults = $this->getDefinition()->getOptionDefaults();

            foreach ($options as $option => $value) {
                if ($value === $optionDefaults[$option]) {
                    unset($options[$option]);
                } else {
                    unset($options[$option]);
                    $options['--' . $option] = $value;
                }
            }

            // Run the specified command with arguments and options
            $exitCode = Artisan::call('backup:run', array_merge($this->input->getArguments(), $options));

            if ($exitCode === 0) {
                $this->info("Command backup:run succeeded on attempt {$attempt}.");
                return 0;
            } else {
                $this->error("Command backup:run failed on attempt {$attempt} (Exit code: {$exitCode}).");
            }
        }

        return 1;
    }
}
