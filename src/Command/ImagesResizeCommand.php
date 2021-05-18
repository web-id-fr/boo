<?php declare(strict_types=1);

namespace App\Command;

use App\Services\ImagesFinder;
use App\utils\ConsoleUtils;
use Intervention\Image\ImageManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

/*
 * ex : php bin/app img:resize /Users/yfrommelt/Sites/gitrepos/s3-synchro-script/var --size 1345
 */
class ImagesResizeCommand extends Command
{
    protected static $defaultName = 'img:resize';

    private ImagesFinder $imagesFinder;
    private ImageManager $imagesManager;
    private Stopwatch $stopwatch;

    public function __construct()
    {
        $this->imagesFinder = new ImagesFinder();
        $this->imagesManager = new ImageManager();
        $this->stopwatch = new Stopwatch();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Resize images');
        $this->addArgument('source', InputArgument::REQUIRED, 'Source images folder');
        $this->addOption('size', null, InputArgument::OPTIONAL, 'Max output image size', 1000);
        $this->addOption('width', null, InputArgument::OPTIONAL, 'Max output image width');
        $this->addOption('height', null, InputArgument::OPTIONAL, 'Max output image height');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section($this->getDescription());

        /** @var string $source */
        $source = $input->getArgument('source');

        $maxWidth = (int)($input->getOption('width') ?? $input->getOption('size'));
        $maxHeight = (int)($input->getOption('height') ?? $input->getOption('size'));

        $io->writeln(sprintf(
            'Scan for images in "%s".',
            $source
        ));

        $images = $this->imagesFinder->findImages($source);

        $this->stopwatch->start('Resize Images');

        $sample = 0; // Change for sample limitation
        $maxProgress = ($sample > 0) ? $sample : count($images);

        $io->progressStart($maxProgress);

        foreach ($images as $imagePath) {
            try {
                $image = $this->imagesManager->make($imagePath);
                // resize the image so that the largest side fits within the limit; the smaller
                // side will be scaled to maintain the original aspect ratio
                $image->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image->save();
            } catch (\Exception $e) {
                $io->error(sprintf("%s\n%s", $e->getMessage(), $imagePath));
            }

            $io->progressAdvance();
            $sample--;
            if ($sample === 0) {
                return Command::SUCCESS;
            }
        }

        $io->progressFinish();
        $timeEvent = $this->stopwatch->stop('Resize Images');
        $time = ConsoleUtils::readableElapsedTime($timeEvent->getDuration());
        $io->success(sprintf(
            'Successfully resize %d images in %s',
            $maxProgress,
            $time,
        ));

        return Command::SUCCESS;
    }
}
