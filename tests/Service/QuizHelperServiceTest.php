<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\QuizParticipation;
use App\Service\QuizHelperService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class QuizHelperServiceTest extends TestCase
{
    private QuizHelperService $quizHelperService;
    private QuizParticipation $quizParticipation;
    private Quiz $quiz;
    private Question $question1;
    private Question $question2;

    protected function setUp(): void
    {
        // Initialize the service
        $this->quizHelperService = new QuizHelperService();

        // Set up common test data
        $this->quiz = new Quiz();
        $this->quiz->setTimeLimitInMinutes(60); // 1-hour limit

        $this->question1 = new Question('What is a duck?');
        $this->quiz->addQuestion($this->question1);
        $this->question2 = new Question('Where do plane live?');
        $this->quiz->addQuestion($this->question2);

        $this->quizParticipation = new QuizParticipation();
        $this->quizParticipation->setQuiz($this->quiz);
        $this->quizParticipation->setStartAt(CarbonImmutable::now()->subMinutes(30)); // Started 30 minutes ago
    }

    public function testIsQuizCompletedReturnsTrueWhenCompletedAtIsSet(): void
    {
        $this->quizParticipation->setCompletedAt(new CarbonImmutable());

        $this->assertTrue($this->quizHelperService->isQuizCompleted($this->quizParticipation));
    }

    public function testIsQuizCompletedReturnsTrueWhenTimeExceeded(): void
    {
        $this->quizParticipation->setStartAt(CarbonImmutable::now()->subHours(2));

        $this->assertTrue($this->quizHelperService->isQuizCompleted($this->quizParticipation));
    }

    public function testIsQuizCompletedReturnsFalseWhenWithinTimeLimit(): void
    {
        $this->quizParticipation->setStartAt(CarbonImmutable::now()->subMinutes(30));

        $this->assertFalse($this->quizHelperService->isQuizCompleted($this->quizParticipation));
    }

    public function testGetNextUnansweredQuestionReturnsNextQuestion(): void
    {
        $answeredQuestion = new Answer(quizParticipation: $this->quizParticipation, question: $this->question1);
        $this->quizParticipation->addAnswer($answeredQuestion);

        $this->assertSame($this->question2, $this->quizHelperService->getNextUnansweredQuestion($this->quizParticipation));
    }

    public function testGetNextUnansweredQuestionReturnsNullWhenAllAnswered(): void
    {
        $answeredQuestion1 = new Answer(quizParticipation: $this->quizParticipation, question: $this->question1);
        $answeredQuestion2 = new Answer(quizParticipation: $this->quizParticipation, question: $this->question2);
        $answeredQuestion2->setQuestion($this->question2);
        $this->quizParticipation->addAnswer($answeredQuestion1);
        $this->quizParticipation->addAnswer($answeredQuestion2);

        $this->assertNull($this->quizHelperService->getNextUnansweredQuestion($this->quizParticipation));
    }

    public function testGetNextUnansweredQuestionReturnsNullWhenNoQuestions(): void
    {
        foreach ($this->quiz->getQuestions() as $question) {
            $this->quiz->removeQuestion($question);
        }

        $this->assertNull($this->quizHelperService->getNextUnansweredQuestion($this->quizParticipation));
    }
}
