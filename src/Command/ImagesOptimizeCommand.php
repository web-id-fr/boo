<?php declare(strict_types=1);

namespace App\Command;

use App\Services\ImagesFinder;
use App\utils\ConsoleUtils;
use Intervention\Image\ImageManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

/*
 * ex : php bin/app img:optimize /Users/yfrommelt/Sites/gitrepos/s3-synchro-script/var
 */
class ImagesOptimizeCommand extends Command
{
    protected static $defaultName = 'img:optimize';

    private ImagesFinder $imagesFinder;
    private Stopwatch $stopwatch;

    public function __construct()
    {
        $this->imagesFinder = new ImagesFinder();
        $this->stopwatch = new Stopwatch();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Optimize images');
        $this->addArgument('source', InputArgument::REQUIRED, 'Source images folder');
        $this->addOption('size', null, InputArgument::OPTIONAL, 'Max output image size', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section($this->getDescription());

        /** @var string $source */
        $source = $input->getArgument('source');

        $io->writeln(sprintf(
            'Scan for images in "%s".',
            $source
        ));

        $images = $this->imagesFinder->findImages($source);

        $this->stopwatch->start('Optimize Images');

        $sample = 10; // Change for sample limitation
        $maxProgress = ($sample > 0) ? $sample : count($images);

        $progressBar = new ProgressBar($output, $maxProgress);
        $progressBar->start();
        foreach ($images as $imagePath) {
            try {

            } catch (\Exception $e) {
                $io->error(sprintf("%s\n%s", $e->getMessage(), $imagePath));
            }

            $progressBar->setMessage('Task is in progress...');
            $progressBar->advance();
            $sample--;
            if ($sample === 0) {
                return Command::SUCCESS;
            }
        }
        $progressBar->finish();

        $timeEvent = $this->stopwatch->stop('Optimize Images');
        $time = ConsoleUtils::readableElapsedTime($timeEvent->getDuration());
        $io->success(sprintf(
            'Successfully optimize %d images in %s',
            $maxProgress,
            $time,
        ));

        return Command::SUCCESS;
    }
}
