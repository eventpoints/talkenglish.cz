<?php

namespace App\Controller\Controller;


use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/user')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly LessonRepository $lessonRepository
    )
    {
    }

    #[Route(path: '/dashboard', name: 'user_dashboard')]
    public function dashboard() : Response
    {
        $lessons = $this->lessonRepository->findAvailableLessons();
        return  $this->render('user/dashboard.html.twig', [
            'lessons' => $lessons
        ]);
    }

}