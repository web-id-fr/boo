<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Webmozart\Assert\Assert;

class BackupS3Successful extends Notification
{
    use Queueable;

    public function __construct(
        private string $source,
        private string $destination,
        private string $logOutput,
        private string $backupSizeOutput,
        private string $duration
    ) {
    }

    // @phpstan-ignore-next-line
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack(): ?SlackMessage
    {
        $channel = config('backup.notifications.slack.channel');
        $username = config('backup.notifications.slack.username');
        $icon = config('backup.notifications.slack.icon');

        Assert::stringNotEmpty($channel);
        Assert::stringNotEmpty($username);
        Assert::stringNotEmpty($icon);

        $slackMessage = new SlackMessage();
        $slackMessage->success();
        $slackMessage->from(
            $username,
            $icon
        );
        $slackMessage->to($channel);

        $content = <<<Markdown
        Successfully synched S3 storage from '$this->source' to '$this->destination' with rclone.
        
        *:new: Changes:*
        {$this->getShortLog()}
        
        *:muscle: Backup Size:*
        $this->backupSizeOutput
        
        *:alarm_clock: Duration:* $this->duration
        Markdown;

        return ($slackMessage)->content($content);
    }

    private function getShortLog():string
    {
        $nbAdded = substr_count($this->logOutput, 'Copied (new)');
        $nbUpdated = substr_count($this->logOutput, 'Copied (replaced existing)');
        $nbDeleted = substr_count($this->logOutput, 'Deleted');

        if ($nbAdded + $nbUpdated + $nbDeleted === 0) {
            return 'No changes.';
        }

        return sprintf(
            'Added: %d, Updated: %d, Deleted: %d',
            $nbAdded,
            $nbUpdated,
            $nbDeleted
        );
    }
}
