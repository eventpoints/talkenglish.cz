<?php

namespace App\Controller\Controller;


use App\Entity\User;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use function Symfony\Component\String\u;

#[Route(path: '/user')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly LessonRepository $lessonRepository
    )
    {
    }

    #[Route(path: '/dashboard', name: 'user_dashboard')]
    public function dashboard(#[CurrentUser] User $currentUser) : Response
    {
        $lessons = $this->lessonRepository->findAvailableLessons(user: $currentUser);
        return  $this->render('user/dashboard.html.twig', [
            'lessons' => $lessons
        ]);
    }

}