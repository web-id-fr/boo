<?php declare(strict_types=1);

namespace App\Command;

use Aws\CommandInterface;
use Aws\S3\S3Client;
use Aws\S3\Transfer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;
use Webmozart\Assert\Assert;

class DownloadDirectoryCommand extends Command
{
    protected static $defaultName = 's3:download-directory';

    private Stopwatch $stopwatch;
    private S3Client $s3Client;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch();
        $this->s3Client = new S3Client([
            'credentials' => [
                'key'    => $_ENV['S3_KEY'],
                'secret' => $_ENV['S3_SECRET'],
            ],
            'region' => $_ENV['S3_REGION'],
            'version' => $_ENV['S3_VERSION'],
            'endpoint' => $_ENV['S3_ENDPOINT'],
        ]);

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Downloading a S3 bucket to a local directory.');
        $this->addArgument('destination', InputArgument::REQUIRED, 'The directory of destination.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section($this->getDescription());

        /** @var string $source */
        $destination = $input->getArgument('destination');

        $source = 's3://'.$_ENV['S3_BUCKET'];

        $io->text(sprintf(
            'Downloading files to directory "%s".',
            $destination
        ));
        $io->newLine();

        $this->stopwatch->start('S3 Download');

        $io->progressStart(5275);

        $transfer = new Transfer($this->s3Client, $source, $destination, [
            'before' => function () use ($io) {
                $io->progressAdvance();
            },
        ]);

        $transfer->transfer();

        $io->progressFinish();
        $timeEvent = $this->stopwatch->stop('S3 Download');

        $fileCount = $this->getFilecount($destination);

        $time = $timeEvent->getDuration();
        $timeInMinutes = floor($time / 1000 / 60);
        $extraSeconds = ($time / 1000) % 60;
        $io->success(sprintf(
            'Succesfuly downloaded %s files in %s:%s',
            $fileCount,
            $timeInMinutes,
            str_pad((string)$extraSeconds, 2, '0', STR_PAD_LEFT),
        ));

        return Command::SUCCESS;
    }

    private function getFilecount(string $directory): int
    {
        $iterator = new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS);

        return iterator_count($iterator);
    }
}
