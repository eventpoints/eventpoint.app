<?php

namespace App\Controller\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/create')]

class CreateController extends AbstractController
{

    #[Route('/', name: 'create_index', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        return $this->render('create/index.html.twig');
    }

}