<?php

declare(strict_types=1);

namespace App\Controller\Controller;


use App\Entity\User;
use App\Enum\FlashEnum;
use App\Form\Form\UserAccountFormType;
use App\Repository\QuizParticipationRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/user')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly QuizParticipationRepository $quizParticipationRepository
    )
    {
    }

    #[Route(path: '/account', name: 'user_account')]
    public function account(#[CurrentUser] User $currentUser, Request $request): Response
    {
        $userAccountForm = $this->createForm(UserAccountFormType::class, $currentUser);
        $userAccountForm->handleRequest(request: $request);
        if ($userAccountForm->isSubmitted() && $userAccountForm->isValid()) {
            $this->userRepository->save(entity: $currentUser, flush: true);
            $this->addFlash(FlashEnum::SUCCESS->value, 'changes saved');
            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'userAccountForm' => $userAccountForm
        ]);
    }

    #[Route(path: '/dashboard', name: 'user_dashboard')]
    public function dashboard(#[CurrentUser] User $currentUser, Request $request): Response
    {
        return $this->render('user/dashboard.html.twig');
    }

    #[Route(path: '/history/quizzes', name: 'user_quiz_history')]
    public function quizzes(#[CurrentUser] User $currentUser): Response
    {
        $quizParticipations = $this->quizParticipationRepository->findByUser(user: $currentUser);
        return $this->render('user/quizzes.html.twig', [
            'quizParticipations' => $quizParticipations
        ]);
    }

    #[Route(path: '/history/lesson', name: 'user_lesson_history')]
    public function lessons(): Response
    {
        return $this->render('user/lessons.html.twig');
    }

}