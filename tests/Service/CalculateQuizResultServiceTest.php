<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Answer;
use App\Entity\AnswerOption;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\QuizParticipation;
use App\Service\QuizResultCalculatorService;
use PHPUnit\Framework\TestCase;

class CalculateQuizResultServiceTest extends TestCase
{
    private QuizResultCalculatorService $quizResultCalculatorService;
    private QuizParticipation $quizParticipation;
    private Question $question1;
    private Question $question2;

    protected function setUp(): void
    {
        $this->quizResultCalculatorService = new QuizResultCalculatorService();

        // Initialize entities
        $quiz = new Quiz();

        $this->question1 = new Question(content: "What is a duck?");
        $this->question2 = new Question(content: "Where is mars?");

        // Add questions to the quiz
        $quiz->addQuestion($this->question1);
        $quiz->addQuestion($this->question2);

        // Create quiz participation
        $this->quizParticipation = new QuizParticipation(quiz: $quiz);

        // Add questions to quiz participation
        $this->quizParticipation->addQuestion($this->question1);
        $this->quizParticipation->addQuestion($this->question2);
    }

    public function testCalculateQuizPercentageWithAllCorrectAnswers(): void
    {
        // Set up correct answer options
        $correctOption1 = new AnswerOption('Correct 1', true);
        $correctOption2 = new AnswerOption('Correct 2', true);

        $this->question1->addAnswerOption($correctOption1);
        $this->question2->addAnswerOption($correctOption2);

        // Simulate user selecting correct answers
        $answer1 = new Answer($this->quizParticipation, $this->question1);
        $answer1->addAnswerOption($correctOption1);

        $answer2 = new Answer($this->quizParticipation, $this->question2);
        $answer2->addAnswerOption($correctOption2);

        $this->quizParticipation->addAnswer($answer1);
        $this->quizParticipation->addAnswer($answer2);

        $result = $this->quizResultCalculatorService->calculateQuizPercentage($this->quizParticipation);
        $this->assertEquals(100.0, $result);
    }

    public function calculateFractalScoreForQuestion(QuizParticipation $quizParticipation, Question $question): float
    {
        $userAnswers = $quizParticipation->getAnswers()->filter(
            fn(Answer $answer): bool => $answer->getQuestion() === $question
        );

        $correctAnswerOptions = $question->getAnswerOptions()->filter(
            fn(AnswerOption $answerOption): bool => $answerOption->getIsCorrect()
        );

        $selectedAnswerOptions = [];
        foreach ($userAnswers as $userAnswer) {
            foreach ($userAnswer->getAnswers() as $selectedOption) {
                $selectedAnswerOptions[] = $selectedOption;
            }
        }

        // Ensure unique selected options to avoid double-counting
        $selectedAnswerOptions = array_unique($selectedAnswerOptions);

        // Count correct and incorrect selections
        $numCorrectSelected = 0;
        foreach ($selectedAnswerOptions as $selectedOption) {
            if ($correctAnswerOptions->exists(fn($key, $correctOption) => $correctOption->getId() === $selectedOption->getId())) {
                $numCorrectSelected++;
            }
        }

        $numCorrectAnswers = $correctAnswerOptions->count();
        $totalSelected = count($selectedAnswerOptions);

        // Adjust score based on correct selections and penalties for incorrect ones
        return $numCorrectAnswers > 0
            ? round(($numCorrectSelected - ($totalSelected - $numCorrectSelected)) / $numCorrectAnswers, 2)
            : 0.0;
    }

    public function testCalculateQuizPercentageWithNoQuestions(): void
    {
        // Create a new QuizParticipation with no questions
        $quiz = new Quiz();
        $quizParticipation = new QuizParticipation();
        $quizParticipation->setQuiz($quiz);

        $result = $this->quizResultCalculatorService->calculateQuizPercentage($quizParticipation);
        $this->assertEquals(0.0, $result);
    }

    public function testCalculateQuizPercentageWithNoCorrectAnswers(): void
    {
        // Set up correct answer options
        $correctOption1 = new AnswerOption('Correct 1', false);
        $correctOption2 = new AnswerOption('Correct 2', false);

        $this->question1->addAnswerOption($correctOption1);
        $this->question2->addAnswerOption($correctOption2);

        // Simulate user selecting incorrect answers
        $incorrectOption1 = new AnswerOption('Incorrect 1', false);
        $incorrectOption2 = new AnswerOption('Incorrect 2', false);

        $answer1 = new Answer($this->quizParticipation, $this->question1);
        $answer1->addAnswerOption($incorrectOption1);

        $answer2 = new Answer($this->quizParticipation, $this->question2);
        $answer2->addAnswerOption($incorrectOption2);

        $this->quizParticipation->addAnswer($answer1);
        $this->quizParticipation->addAnswer($answer2);

        $result = $this->quizResultCalculatorService->calculateQuizPercentage($this->quizParticipation);
        $this->assertEquals(0.0, $result);
    }

    public function testCalculateFractalScoreForQuestionWithAllCorrectAnswers(): void
    {
        // Set up correct answer options
        $correctOption1 = new AnswerOption('Correct 1', true);
        $correctOption2 = new AnswerOption('Correct 2', true);

        $this->question1->addAnswerOption($correctOption1);
        $this->question1->addAnswerOption($correctOption2);

        // Simulate user selecting correct answers
        $answer = new Answer($this->quizParticipation, $this->question1);
        $answer->addAnswerOption($correctOption1);
        $answer->addAnswerOption($correctOption2);

        $this->quizParticipation->addAnswer($answer);

        $score = $this->quizResultCalculatorService->calculateFractalScoreForQuestion($this->quizParticipation, $this->question1);
        $this->assertEquals(1.0, $score); // All correct answers
    }
}
