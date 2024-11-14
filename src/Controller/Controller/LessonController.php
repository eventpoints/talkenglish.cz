<?php

namespace App\Controller\Controller;

use App\Entity\Comment;
use App\Entity\Lesson;
use App\Entity\LessonParticipant;
use App\Entity\User;
use App\Form\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\LessonRepository;
use App\Security\Voter\LessonVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/lesson', name: '')]
class LessonController extends AbstractController
{
    public function __construct(
        private readonly LessonRepository  $lessonRepository,
        private readonly CommentRepository $commentRepository,
        private readonly HubInterface $hub
    )
    {
    }

    #[Route(path: '/view/{id}', name: 'show_lesson')]
    public function show(Lesson $lesson, #[CurrentUser] User $currentUser, Request $request): Response
    {
        $this->denyAccessUnlessGranted(attribute: LessonVoter::VIEW, subject: $lesson, message: 'Sorry, you are not allow here.');

        $comment = new Comment(owner: $currentUser, lesson: $lesson);
        $commentForm = $this->createForm(CommentFormType::class, $comment);

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->commentRepository->save(entity: $comment, flush: true);

            $this->hub->publish(new Update(
                "lesson_comments_".$lesson->getId(),
                $this->renderView('comment/comment.stream.html.twig', ['comment' => $comment, 'lesson' => $lesson])
            ));
            return $this->redirectToRoute('show_lesson', ['id' => $lesson->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'commentForm' => $commentForm
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