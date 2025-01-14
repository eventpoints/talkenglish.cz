<?php

declare(strict_types=1);

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

    #[Route(path: '/about-us', name: 'about_us')]
    public function about() : Response
    {
        return $this->render('app/about-us.html.twig');
    }

    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function sitemap(): Response
    {
        return $this->render('app/sitemap.xml.twig', [], new Response(
            '',
            Response::HTTP_OK,
            ['Content-Type' => 'application/xml']
        ));
    }
}
