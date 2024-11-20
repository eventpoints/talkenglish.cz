<?php

namespace App\Controller\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{

    #[Route(path: '/price-list', name: 'price_list')]
    public function priceList() : Response
    {
        return $this->render('app/price-list.html.twig');

    }
}