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
use App\Repository\UserRepository;
use App\Service\AvatarService;
use App\Service\GuestNameGenerator;
use App\Service\QuizHelperService;
use App\Service\QuizResultCalculatorService;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/quizzes')]
class QuizController extends AbstractController
{

    public function __construct(
        private readonly QuizRepository              $quizRepository,
        private readonly QuizParticipationRepository $quizParticipationRepository,
        private readonly AnswerRepository            $answerRepository,
        private readonly PaginatorInterface          $paginator,
        private readonly QuizHelperService           $quizHelperService,
        private readonly EntityManagerInterface      $entityManager,
        private readonly QuizResultCalculatorService $quizResultCalculatorService,
        private readonly GuestNameGenerator          $guestNameGenerator,
        private readonly UserRepository              $userRepository,
        private readonly AvatarService               $avatarService,
        private readonly Security                    $security,
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


    #[Route(path: '/start/{slug}', name: 'quiz_start')]
    public function startQuiz(
        #[MapEntity(mapping: [
            'slug' => 'slug',
        ])]
        Quiz $quiz, Request $request): Response
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $quizParticipation = $this->quizParticipationRepository->findInProgressByUser($quiz, $user);
            if ($quizParticipation instanceof QuizParticipation) {
                return $this->redirectToRoute('quiz_in_progress', ['id' => $quizParticipation->getId()]);
            }
        }

        if ($request->isMethod('POST')) {
            /** @var ?User $user */
            $user = $this->security->getUser();

            if (!$user instanceof User) {
                $fingerprint = $request->getSession()->get('fingerprint');
                $user = $this->userRepository->findOneBy(['fingerprint' => $fingerprint]);

                if (!$user instanceof User) {
                    $avatar = $this->avatarService->createAvatar(hashString: $fingerprint);
                    $user = new User(
                        firstName: $this->guestNameGenerator->generateFirstName(),
                        lastName: $this->guestNameGenerator->generateLastName(),
                        avatar: $avatar,
                        fingerprint: $fingerprint
                    );
                    $this->userRepository->save(entity: $user, flush: true);
                }

                $this->security->login(user: $user, authenticatorName: 'security.authenticator.form_login.main');
            }

            $quizParticipation = new QuizParticipation(owner: $user, quiz: $quiz);
            $now = new CarbonImmutable();
            $quizParticipation->setStartAt($now);
            $quizParticipation->setEndAt($now->addMinutes($quiz->getTimeLimitInMinutes()));

            $this->quizParticipationRepository->save($quizParticipation, true);

            return $this->redirectToRoute('quiz_in_progress', [
                'id' => $quizParticipation->getId(),
            ]);
        }

        return $this->render('quiz/start.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route(path: '/pre-start/{id}', name: 'quiz_pre_start_redirect')]
    public function redirectPreStart(Quiz $quiz): RedirectResponse
    {
        return $this->redirectToRoute('quiz_start', ['slug' => $quiz->getSlug()]);
    }

    #[Route(path: '/result/{id}', name: 'quiz_result')]
    public function quizResult(QuizParticipation $quizParticipation): Response
    {
        $quizParticipationStatistic = $this->quizParticipationRepository->getUserPerformanceStats(quizParticipation: $quizParticipation);
        $relatedQuizzes = $this->quizRepository->findRelatedByQuiz(quiz: $quizParticipation->getQuiz());

        return $this->render(
            view: '/quiz/result.html.twig',
            parameters: [
                'quizParticipationStatistic' => $quizParticipationStatistic,
                'quizParticipation' => $quizParticipation,
                'relatedQuizzes' => $relatedQuizzes
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
            $this->quizHelperService->completeQuiz(quizParticipation: $quizParticipation, quizResultCalculatorService: $this->quizResultCalculatorService);
            $this->entityManager->persist($quizParticipation);
            $this->entityManager->flush();

            return $this->redirectToRoute('quiz_result', ['id' => $quizParticipation->getId()]);
        }
        $question = $this->quizHelperService->getNextUnansweredQuestion($quizParticipation);

        if (!$question instanceof Question) {
            $this->quizHelperService->completeQuiz(quizParticipation: $quizParticipation, quizResultCalculatorService: $this->quizResultCalculatorService);
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
            $this->quizHelperService->completeQuiz(quizParticipation: $quizParticipation, quizResultCalculatorService: $this->quizResultCalculatorService);
        }
    }


}