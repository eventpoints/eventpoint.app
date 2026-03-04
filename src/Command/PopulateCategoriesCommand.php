<?php

declare(strict_types=1);

namespace App\Command;

use App\Data\Categories;
use App\Entity\Event\Category;
use App\Factory\CategoryFactory;
use App\Repository\Event\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:categories:populate',
    description: 'Populate the category table with the standard EventPoint category list.',
)]
class PopulateCategoriesCommand extends Command
{
    /**
     * Maps old/legacy category keys to their canonical replacements.
     * Events attached to an old key will be re-tagged to the new one before the old row is pruned.
     *
     * @var array<string, string>
     */
    private const array MIGRATIONS = [
        'category.comedy-show' => 'category.arts_and_culture.comedy',
        'category.gaming.board_game_night' => 'category.hobby.board_games',
        'category.business_networking' => 'category.social.networking',
        'category.social.after-work-drink' => 'category.social.after_work_drinks',
        'category.cycling_tournament' => 'category.outdoor.cycling',
    ];

    public function __construct(
        private readonly Categories $categories,
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryFactory $categoryFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview changes without writing to the database')
            ->addOption('prune', null, InputOption::VALUE_NONE, 'Remove categories not in the canonical list, migrating attached events where a mapping exists');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');
        $prune = (bool) $input->getOption('prune');

        if ($dryRun) {
            $io->note('Dry-run mode — no changes will be written.');
        }

        $canonicalTitles = array_map('strtolower', $this->categories->getCategories());

        // --- Prune non-canonical categories ---
        $pruned = 0;
        $migrated = 0;
        $skippedInUse = 0;

        if ($prune) {
            $io->section('Pruning non-canonical categories');

            foreach ($this->categoryRepository->findAll() as $category) {
                $title = (string) $category->getTitle();

                if (in_array(strtolower($title), $canonicalTitles, strict: true)) {
                    continue;
                }

                $eventCount = $category->getEvents()->count();

                if ($eventCount === 0) {
                    $io->writeln(sprintf('  <fg=red>prune</>  %s', $title));
                    if (! $dryRun) {
                        $this->entityManager->remove($category);
                    }
                    ++$pruned;
                    continue;
                }

                $newKey = self::MIGRATIONS[$title] ?? null;

                if ($newKey === null) {
                    $io->writeln(sprintf(
                        '  <comment>keep</>   %s <fg=yellow>(attached to %d event%s, no migration mapping)</>',
                        $title,
                        $eventCount,
                        $eventCount === 1 ? '' : 's',
                    ));
                    ++$skippedInUse;
                    continue;
                }

                $replacement = $this->categoryRepository->findOneBy([
                    'title' => $newKey,
                ])
                    ?? $this->categoryFactory->create(title: $newKey);

                $io->writeln(sprintf(
                    '  <info>migrate</> %s → %s (%d event%s)',
                    $title,
                    $newKey,
                    $eventCount,
                    $eventCount === 1 ? '' : 's',
                ));

                if (! $dryRun) {
                    foreach ($category->getEvents() as $event) {
                        $event->addCategory($replacement);
                        $event->removeCategory($category);
                    }
                    $this->entityManager->persist($replacement);
                    $this->entityManager->remove($category);
                }

                ++$migrated;
                ++$pruned;
            }

            if (! $dryRun && $pruned > 0) {
                $this->entityManager->flush();
            }

            $io->newLine();
        }

        // --- Add missing canonical categories ---
        $io->section('Adding missing categories');

        $existingTitles = array_map(
            fn (Category $c) => strtolower((string) $c->getTitle()),
            $this->categoryRepository->findAll(),
        );

        $created = 0;
        $alreadyPresent = 0;

        foreach ($this->categories->getCategories() as $title) {
            if (in_array(strtolower($title), $existingTitles, strict: true)) {
                ++$alreadyPresent;
                continue;
            }

            $io->writeln(sprintf('  <info>add</>    %s', $title));

            if (! $dryRun) {
                $this->entityManager->persist($this->categoryFactory->create(title: $title));
            }

            ++$created;
        }

        if (! $dryRun && $created > 0) {
            $this->entityManager->flush();
        }

        $io->newLine();

        if ($prune) {
            $io->success(sprintf(
                '%d pruned (%d migrated), %d kept (no mapping)  —  %d added, %d already present%s.',
                $pruned,
                $migrated,
                $skippedInUse,
                $created,
                $alreadyPresent,
                $dryRun ? ' (dry-run)' : '',
            ));
        } else {
            $io->success(sprintf(
                '%d categor%s added, %d already present%s.',
                $created,
                $created === 1 ? 'y' : 'ies',
                $alreadyPresent,
                $dryRun ? ' (dry-run)' : '',
            ));
        }

        return Command::SUCCESS;
    }
}
