<?php

namespace App\Console\Commands;

use App\Notifications\BackupS3Successful;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Process\Process;
use Symfony\Component\Stopwatch\Stopwatch;
use Webmozart\Assert\Assert;

use function Safe\file_get_contents;
use function Safe\set_time_limit;

class BackupS3Sync extends Command
{
    protected $signature = 'backup:s3-sync {source} {destination} {--with-delete}';

    protected $description = 'Backup S3 storage into another storage with rclone';

    private string $logOutput = '';
    private string $backupSizeOutput = '';
    private Carbon $startedAt;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // no time limit
        set_time_limit(0);

        $this->startedAt = Carbon::now();

        $this->comment(sprintf(
            'Backuping S3 storage (%s) into another storage (%s) with rclone…',
            $this->getSource(),
            $this->getDestination()
        ));

        $this->checkRcloneSetup();

        $this->synchronize();

        $this->info(sprintf(
            'Backup S3 storage (%s) into another storage (%s) with rclone successful.',
            $this->getSource(),
            $this->getDestination()
        ));

        // print duration
        $duration = str_replace(' ago', '', $this->startedAt->diffForHumans());
        $this->info(sprintf('Duration: %s', $duration));

        $notification = new BackupS3Successful(
            $this->getSource(),
            $this->getDestination(),
            $this->logOutput,
            $this->backupSizeOutput,
            $duration
        );

        Notification::route('slack', config('backup.notifications.slack.webhook_url'))
            ->notifyNow($notification);

        return 0;
    }

    protected function synchronize(): void
    {
        // cleaning log file
        $process = Process::fromShellCommandline('echo "" > /tmp/rclone.log');
        $process->mustRun();

        // copy does not delete files that are not in the source
        $rcloneCommand = 'copy';

        if ($this->option('with-delete')) {
            // sync deletes files that are not in the source
            $rcloneCommand = 'sync';
        }

        $process = Process::fromShellCommandline(sprintf(
            // using log-file because rclone does not support stdout
            'rclone %s %s %s --verbose --log-file=/tmp/rclone.log',
            $rcloneCommand,
            $this->getSource(),
            $this->getDestination()
        ));

        $process->setTimeout(0);

        $process->mustRun();

        // retrieving log file
        $this->logOutput = file_get_contents('/tmp/rclone.log');
        $this->line($this->logOutput);

        // getting backup total size (destination)
        $process = Process::fromShellCommandline(sprintf(
            'rclone size %s',
            $this->getDestination()
        ));

        $this->backupSizeOutput = $process->mustRun()->getOutput();
        $this->line($this->backupSizeOutput);
    }

    protected function checkRcloneSetup(): void
    {
        $process = new Process(['rclone', '--version']);
        $process->run();

        Assert::true($process->isSuccessful(), 'rclone is not installed');
    }

    protected function getSource(): string
    {
        $source = $this->argument('source');
        Assert::notEmpty($source);

        return $source;
    }

    protected function getDestination(): string
    {
        $destination = $this->argument('destination');
        Assert::notEmpty($destination);

        return $destination;
    }
}
