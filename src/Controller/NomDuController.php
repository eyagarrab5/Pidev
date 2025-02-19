<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NomDuController extends AbstractController
{
    #[Route('/Admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('Admin/home.html.twig', [
            'controller_name' => 'admin',
        ]);
    }
}
