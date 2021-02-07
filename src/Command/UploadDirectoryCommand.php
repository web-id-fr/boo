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

class UploadDirectoryCommand extends Command
{
    protected static $defaultName = 's3:upload-directory';

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
        $this->setDescription('Uploading a directory to a S3 bucket.');
        $this->addArgument('source', InputArgument::REQUIRED, 'The directory to upload.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section($this->getDescription());

        /** @var string $source */
        $source = $input->getArgument('source');
        Assert::directory($source, sprintf('Source "%s" is not a valid directory', $source));

        $fileCount = $this->getFilecount($source);
        Assert::greaterThan($fileCount, 0, sprintf('Source "%s" directory is empty.', $source));

        $destination = 's3://'.$_ENV['S3_BUCKET'];

        $io->text(sprintf(
            'Uploading %s files from directory "%s".',
            $fileCount,
            $source
        ));
        $io->newLine();

        $this->stopwatch->start('S3 Upload');

        $io->progressStart($fileCount);

        $transfer = new Transfer($this->s3Client, $source, $destination, [
            'before' => function (CommandInterface $command) use ($io) {
                // sets files in public (files are private by default)
                if (in_array($command->getName(), ['PutObject', 'CreateMultipartUpload'])) {
                    $command['ACL'] = 'public-read';

                    $io->progressAdvance();
                }
            },
        ]);

        $transfer->transfer();

        $io->progressFinish();
        $timeEvent = $this->stopwatch->stop('S3 Upload');

        $time = $timeEvent->getDuration();
        $timeInMinutes = floor($time / 1000 / 60);
        $extraSeconds = ($time / 1000) % 60;
        $io->success(sprintf(
            'Succesfuly uploaded %s files in %s:%s',
            $fileCount,
            $timeInMinutes,
            str_pad((string)$extraSeconds, 2, '0', STR_PAD_LEFT),
        ));

        return Command::SUCCESS;
    }

    private function getFilecount(string $directory): int
    {
        $iterator = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);

        return iterator_count($iterator);
    }
}
