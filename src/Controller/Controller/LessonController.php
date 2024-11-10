<?php

namespace App\Controller\Controller;

use App\Entity\Lesson;
use App\Entity\LessonParticipant;
use App\Entity\User;
use App\Repository\LessonRepository;
use App\Security\Voter\LessonVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/lesson', name: '')]
class LessonController extends AbstractController
{


    public function __construct(
        private readonly LessonRepository $lessonRepository
    )
    {
    }

    #[Route(path: '/view/{id}', name: 'show_lesson')]
    public function show(Lesson $lesson): Response
    {
//        $this->denyAccessUnlessGranted(attribute: LessonVoter::VIEW, subject: $lesson, message: 'Sorry, you are not allow here.');
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson
        ]);
    }

    #[Route(path: '/join/{id}', name: 'join_lesson_redirect')]
    public function join(Lesson $lesson, #[CurrentUser] User $currentUser): Response
    {
        $lessonParticipant = new LessonParticipant();
        $lessonParticipant->setLesson($lesson);
        $lessonParticipant->setOwner($currentUser);
        $lesson->addLessonParticipant($lessonParticipant);
        $this->lessonRepository->save(entity: $lesson, flush: true);

        return $this->redirectToRoute('show_lesson', ['id' => $lesson->getId()]);
    }

}