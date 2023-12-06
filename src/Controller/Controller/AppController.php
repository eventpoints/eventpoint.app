<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route(path: '/terms', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('app/terms.html.twig');
    }

    #[Route(path: '/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('app/about.html.twig');
    }
}
