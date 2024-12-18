<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\DataTransferObject\QuizFilterDto;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\QuizParticipation;
use App\Entity\User;
use App\Form\Filter\QuizFilterType;
use App\Form\Form\Quiz\AnswerFormType;
use App\Repository\AnswerRepository;
use App\Repository\QuizParticipationRepository;
use App\Repository\QuizRepository;
use App\Service\QuizHelperService;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


#[Route(path: '/quizzes')]
class QuizController extends AbstractController
{

    public function __construct(
        private readonly QuizRepository              $quizRepository,
        private readonly QuizParticipationRepository $quizParticipationRepository,
        private readonly AnswerRepository            $answerRepository,
        private readonly PaginatorInterface          $paginator,
        private readonly QuizHelperService           $quizHelperService,
        private readonly EntityManagerInterface      $entityManager
    )
    {
    }

    #[Route(path: '/', name: 'quizzes')]
    public function index(Request $request): Response
    {
        $quizFilterDto = new QuizFilterDto();
        $quizQuery = $this->quizRepository->findByQuizFilterDto(quizFilterDto: $quizFilterDto, isQuery: true);
        $quizzesPagination = $this->paginator->paginate(target: $quizQuery, page: $request->query->getInt('page', 1), limit: 30);
        $quizFilter = $this->createForm(QuizFilterType::class, $quizFilterDto);

        $quizFilter->handleRequest($request);
        if ($quizFilter->isSubmitted() && $quizFilter->isValid()) {
            $quizQuery = $this->quizRepository->findByQuizFilterDto(quizFilterDto: $quizFilterDto, isQuery: true);
            $quizzesPagination = $this->paginator->paginate(target: $quizQuery, page: $request->query->getInt('page', 1), limit: 30);

            return $this->render('quiz/index.html.twig', [
                'quizFilter' => $quizFilter,
                'quizzesPagination' => $quizzesPagination
            ]);
        }

        return $this->render('quiz/index.html.twig', [
            'quizFilter' => $quizFilter,
            'quizzesPagination' => $quizzesPagination
        ]);
    }


    #[Route(path: '/pre-start/{id}', name: 'quiz_start')]
    public function startQuiz(Quiz $quiz, Request $request, #[CurrentUser] User $currentUser): Response
    {
        $quizParticipation = $this->quizParticipationRepository->findInProgressByUser(quiz: $quiz, user: $currentUser);

        if ($quizParticipation instanceof QuizParticipation) {
            return $this->redirectToRoute('quiz_in_progress', ['id' => $quizParticipation->getId()]);
        }

        $quizParticipation = new QuizParticipation(
            owner: $currentUser,
            quiz: $quiz
        );

        if ($request->isMethod('POST')) {
            $quizParticipation->setStartAt(new CarbonImmutable());
            $quizParticipation->setEndAt($quizParticipation->getStartAt()->addMinutes($quiz->getTimeLimitInMinutes()));
            $this->quizParticipationRepository->save(entity: $quizParticipation, flush: true);
            return $this->redirectToRoute('quiz_in_progress', [
                'id' => $quizParticipation->getId(),
            ]);
        }

        $previousParticipations = $this->quizParticipationRepository->findByUser(quiz: $quiz, user: $currentUser);

        return $this->render('quiz/pre_start.html.twig', [
            'quiz' => $quiz,
            'quizParticipation' => $quizParticipation,
            'previousParticipations' => $previousParticipations
        ]);
    }

    #[Route(path: '/result/{id}', name: 'quiz_result')]
    public function quizResult(QuizParticipation $quizParticipation, #[CurrentUser] User $currentUser): Response
    {
        return $this->render(
            view: '/quiz/result.html.twig',
            parameters: [
                'quizParticipation' => $quizParticipation,
            ]
        );
    }

    #[Route(path: '/progress/{id}', name: 'quiz_in_progress')]
    public function quizInProgress(
        Request           $request,
        QuizParticipation $quizParticipation,
    ): Response
    {
        if ($this->quizHelperService->isQuizCompleted($quizParticipation)) {
            $quizParticipation->setCompletedAt(new CarbonImmutable());
            $this->entityManager->persist($quizParticipation);
            $this->entityManager->flush();

            return $this->redirectToRoute('quiz_result', ['id' => $quizParticipation->getId()]);
        }
        $question = $this->quizHelperService->getNextUnansweredQuestion($quizParticipation);

        if (!$question instanceof Question) {
            $quizParticipation->setCompletedAt(new CarbonImmutable());
            $this->entityManager->persist($quizParticipation);
            $this->entityManager->flush();
            return $this->redirectToRoute('quiz_result', ['id' => $quizParticipation->getId()]);
        }

        $answer = new Answer(quizParticipation: $quizParticipation, question: $question);
        $answerForm = $this->createForm(AnswerFormType::class, $answer, ['question' => $question]);

        $answerForm->handleRequest($request);
        if ($answerForm->isSubmitted() && $answerForm->isValid()) {
            $this->handleAnswerSubmission($quizParticipation, $answer);

            return $this->redirectToRoute('quiz_in_progress', ['id' => $quizParticipation->getId()]);
        }

        return $this->render('/quiz/in_progress.html.twig', [
            'question' => $question,
            'quizParticipation' => $quizParticipation,
            'answerForm' => $answerForm,
        ]);
    }


    public function handleAnswerSubmission(
        QuizParticipation $quizParticipation,
        Answer            $answer,
    ): void
    {
        $this->answerRepository->save($answer, flush: true);
        $this->quizParticipationRepository->save($quizParticipation, flush: true);

        if ($this->quizHelperService->isQuizCompleted($quizParticipation)) {
            $quizParticipation->setCompletedAt(new CarbonImmutable());
        }
    }


}