<?php

namespace App\Command;

use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Enum\Quiz\CategoryEnum;
use App\Enum\Quiz\LevelEnum;
use App\Enum\Quiz\QuestionTypeEnum;
use App\Repository\QuestionRepository;
use OpenAI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-questions',
    description: 'Generates questions and answers using the OpenAI API.'
)]
class GenerateQuestionsCommand extends Command
{

    public function __construct(
        private readonly QuestionRepository $questionRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('number', InputArgument::REQUIRED, 'number of questions to generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);
        $number = (int)$input->getArgument('number');

        for ($count = 0; $count < $number; $count++) {
            $io->info('Generating questions...');

            $category = CategoryEnum::GENERAL;
            $level = LevelEnum::A1;
            $questionType = QuestionTypeEnum::MULTIPLE_CHOICE;
            $question = new Question(questionTypeEnum: $questionType, categoryEnum: $category, levelEnum: $level, timeLimitInSeconds: 20);

            $client = OpenAI::client(apiKey: $_ENV['OPENAI_API_KEY']);
            $questionResult = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a helpful assistant creating closed-ended, objective questions designed to teach English language concepts. These questions should have a specific, factual answer. Each question should focus on topics like vocabulary, grammar rules, parts of speech, or general knowledge. Only generate questions! never create any answers! Respond with only text!",
                    ],
                    [
                        'role' => 'user',
                        'content' => "Create one $level->name question for $category->name (not multiple choice!)",
                    ],
                ],
            ]);

            $questionContent = $questionResult->choices[0]->message->content;
            $output->writeln("Received response: $questionContent");
            $question->setContent($questionContent);

            $answersResult = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You create multiple choice answer to English language questions one or more may be correct. resposne with an comma seperated string like 'answer content,true/false (is correct?) ____ answer content,true/false (is correct?) ____ answer content,true/false (is correct?)' ",
                    ],
                    [
                        'role' => 'user',
                        'content' => "Create at least 2 answer options for this question: '$questionContent'",
                    ],
                ],
            ]);
            $answerContent = $answersResult->choices[0]->message->content;
            $answerParts = explode('____', $answerContent);
            foreach ($answerParts as $answerPart) {
                $parts = explode(',', $answerPart);
                $answerOption = new AnswerOption(content: $parts[0], isCorrect: (bool)$parts[1], question: $question);
                $question->addAnswerOption($answerOption);
            }

            $this->questionRepository->save(entity: $question, flush: true);
            $io->info('question added');
        }

        $io->success('complete!');
        return Command::SUCCESS;
    }
}