<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class NomDuController extends AbstractController
{
    #[Route('/Admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('Admin/home.html.twig', [
            'controller_name' => 'admin',
        ]);
    }

    #[Route('/admin/statistiques', name: 'admin_statistiques')]
    public function indexStats(): Response
    {
    
        $dataProps = [5, 12, 9, 7, 15];  // Remplacer par des données réelles
        $dataResv = [3, 9, 12, 6, 8];    // Remplacer par des données réelles
        $dataDemands = [6, 14, 11, 8, 10];  // Remplacer par des données réelles
        $dataOffers = [7, 13, 10, 5, 12];   // Remplacer par des données réelles

        // Passer ces données à la vue
        return $this->render('Admin/statistiques.html.twig', [
            'dataProps' => $dataProps,
            'dataResv' => $dataResv,
            'dataDemands' => $dataDemands,
            'dataOffers' => $dataOffers
        ]);
    }
}
