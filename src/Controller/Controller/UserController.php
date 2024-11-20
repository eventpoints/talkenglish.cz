<?php

namespace App\Controller\Controller;


use App\Entity\User;
use App\Enum\FlashEnum;
use App\Form\Form\UserAccountFormType;
use App\Repository\LessonRepository;
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
        private readonly LessonRepository $lessonRepository,
        private readonly UserRepository   $userRepository
    )
    {
    }

    #[Route(path: '/dashboard', name: 'user_dashboard')]
    public function dashboard(): Response
    {
        $lessons = $this->lessonRepository->findAvailableLessons();
        return $this->render('user/dashboard.html.twig', [
            'lessons' => $lessons
        ]);
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

    #[Route(path: '/quizzes', name: 'user_quizzes')]
    public function quizzes(): Response
    {
        return $this->render('user/quizzes.html.twig');
    }

}