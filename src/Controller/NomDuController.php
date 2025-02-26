<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\DemandeCovoiturage;
use App\Entity\OffreCovoiturage;
use App\Entity\PropositionCovoiturage;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

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
    public function indexStats(EntityManagerInterface $em): Response
    {
        // Générer les étiquettes de date pour les 5 derniers jours
        $dateLabels = [];
        for ($i = 4; $i >= 0; $i--) {
            $date = new \DateTime("today - $i days");
            $dateLabels[] = $date->format('d-m'); // Format des dates sous forme 'jour-mois'
        }
    
        $startDate = (new \DateTime('today -4 days'))->setTime(0, 0, 0);
        $endDate = (new \DateTime())->setTime(23, 59, 59);
    
        // Fonction pour récupérer et traiter les données
        $fetchData = function ($entityClass, $dateField) use ($em, $startDate, $endDate, $dateLabels) {
            $repository = $em->getRepository($entityClass);
            $entities = $repository->createQueryBuilder('e')
                ->where("e.$dateField BETWEEN :start AND :end")
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getResult();
    
            // Initialiser les données avec 0 pour chaque jour
            $data = array_fill_keys($dateLabels, 0);
    
            // Remplir les données en fonction des entités récupérées
            foreach ($entities as $entity) {
                $method = 'get' . ucfirst($dateField); // getDate ou getCreatedAt
                $date = $entity->$method();
    
                if ($date instanceof \DateTime) {
                    $day = $date->format('d-m'); // Format de la date 'jour-mois'
                    if (isset($data[$day])) {
                        $data[$day]++;
                    }
                }
            }
    
            return array_values($data);
        };
    
        // Récupération des données pour chaque entité
        $dataDemands = $fetchData(DemandeCovoiturage::class, 'date');
        $dataOffers = $fetchData(OffreCovoiturage::class, 'date');
        $dataProps = $fetchData(PropositionCovoiturage::class, 'createdAt');
        $dataResv = $fetchData(Reservation::class, 'createdAt');
    
        // Rendre la vue avec les données des courbes et les étiquettes de date
        return $this->render('Admin/statistiques.html.twig', [
            'dataProps' => $dataProps,
            'dataResv' => $dataResv,
            'dataDemands' => $dataDemands,
            'dataOffers' => $dataOffers,
            'dataLabels' => $dateLabels,
        ]);
    }
    
}
