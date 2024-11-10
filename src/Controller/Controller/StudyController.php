<?php

namespace App\Controller\Controller;

use App\DataTransferObject\WordAssociationDto;
use App\Entity\Lesson;
use App\Form\Form\WordAssociationFormType;
use App\Service\WordAssociationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class StudyController extends AbstractController
{


    public function __construct(
        private readonly WordAssociationService $wordAssociationService
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     */
    #[Route(path: '/study-room', name: 'study_room')]
    public function studyRoom(Request $request): Response
    {
        $session = $request->getSession();
        $currentWord = $session->get('currentWord', 'dog');
        $wordAssociationDto = new WordAssociationDto(currentWord: $currentWord, potentiallyRelatedWord: null);
        $wordAssociationForm = $this->createForm(WordAssociationFormType::class, $wordAssociationDto);

        $wordAssociationForm->handleRequest($request);
        if ($wordAssociationForm->isSubmitted() && $wordAssociationForm->isValid()) {
            $isRelated = $this->wordAssociationService->isRelatedWord(
                currentWord: $wordAssociationDto->getCurrentWord(),
                potentiallyRelatedWord: $wordAssociationDto->getPotentiallyRelatedWord()
            );

            if ($isRelated) {
                $session->set('currentWord', $wordAssociationDto->getPotentiallyRelatedWord());
                $this->addFlash('success', 'Nice job!');
            } else {
                $this->addFlash('danger', 'Sorry, that is not a related word. Try again.');
            }
            return $this->redirectToRoute('study_room');
        }

        return $this->render('study/room.html.twig', [
            'wordAssociationForm' => $wordAssociationForm->createView(),
            'currentWord' => $currentWord,
        ]);
    }


}