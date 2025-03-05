<?php

namespace App\Controller; // ✅ Ce namespace doit correspondre au chemin du fichier

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StarterPageController extends AbstractController // ✅ Le nom de la classe doit correspondre au fichier
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('starter_page/index.html.twig');
    }

    #[Route('/starter_page', name: 'starter_page')]
    public function starterPage(): Response
    {
        return $this->render('starter_page/starter_page.html.twig');
    }
}
