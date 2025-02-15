<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\QuizRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(name: 'slugify-quiz-titles')]
class SlugifyQuizTitleCommand extends Command
{

    public function __construct(
        private readonly QuizRepository $quizRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);
        $quizzes = $this->quizRepository->findAll();
        $slugger = new AsciiSlugger();

        foreach ($quizzes as $quiz) {
            $title = strtolower($quiz->getTitle());
            $slug = $slugger->slug(string: $title, locale: 'en');
            $quiz->setSlug($slug->toString());
            $this->quizRepository->save(entity: $quiz, flush: true);
            $io->info("set quiz slug: {$slug->toString()}");
        }

        $io->success("complete");
        return Command::SUCCESS;
    }

}
