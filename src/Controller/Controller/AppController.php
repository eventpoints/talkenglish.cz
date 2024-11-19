<?php

namespace App\Controller\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route(path: '/', name: 'landing')]
    public function index() : Response
    {
        return $this->render('app/landing.html.twig');
    }

}