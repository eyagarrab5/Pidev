<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;





#[Route('/vehicule')]
final class VehiculeController extends AbstractController
{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
public function index(VehiculeRepository $vehiculeRepository, Request $request, PaginatorInterface $paginator): Response
{
    // Récupérer le critère de tri depuis les paramètres de requête
    $sortBy = $request->query->get('sort_by', 'id'); // Par défaut, tri par ID
    $search = $request->query->get('search');
    // Récupérer les véhicules épinglés
    $pinnedVehicules = $vehiculeRepository->findPinnedPosts($search, $sortBy);
    // Créer une requête de base
// Créer une requête de base pour les autres véhicules
$queryBuilder = $vehiculeRepository->createQueryBuilder('v')
->where('v.isPinned = :isPinned')
->setParameter('isPinned', false);
if ($search) {
        $queryBuilder->andWhere('v.modele LIKE :search OR v.typeVehicule LIKE :search OR v.role LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }
    // Appliquer le filtre en fonction du critère de tri
    switch ($sortBy) {
        case 'disponibilite_matin':
            $queryBuilder->andWhere('v.disponibilite = :disponibilite')
                ->setParameter('disponibilite', 'matin');
            break;
        case 'disponibilite_nuit':
            $queryBuilder->andWhere('v.disponibilite = :disponibilite')
                ->setParameter('disponibilite', 'nuit');
            break;
        default:
            // Par défaut, trier par ID
            $queryBuilder->orderBy('v.id', 'ASC');
            break;
    }

    // Paginer les résultats
    $vehicules = $paginator->paginate(
        $queryBuilder->getQuery(), // Requête à paginer
        $request->query->getInt('page', 1), // Numéro de page par défaut
        6 // Nombre d'éléments par page
    );
    if ($request->isXmlHttpRequest()) {
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehicules,
            'pinnedVehicules' => $pinnedVehicules,
        ]);
    }


    return $this->render('vehicule/index.html.twig', [
        'vehicules' => $vehicules,
        'pinnedVehicules' => $pinnedVehicules,
        'sort_by' => $sortBy,
    ]);
}
#[Route('/back',name: 'backk', methods: ['GET'])]
    public function ind3ex(VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('vehicule/inback.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('vehicule_images_directory'), $newFilename);
                    $vehicule->setImage($newFilename);
                    print_r($vehicule->getImage());
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_vehicule_new');
                }
            }

            $entityManager->persist($vehicule);
            $entityManager->flush();

            $this->addFlash('success', 'Véhicule créé avec succès.');
            return $this->redirectToRoute('app_vehicule_index');
        }

        return $this->render('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('vehicule_images_directory'), $newFilename);
                    $vehicule->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_vehicule_edit', ['id' => $vehicule->getId()]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Véhicule mis à jour avec succès.');
            return $this->redirectToRoute('app_vehicule_index');
        }

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }


    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backk');
    }
    
#[Route('/vehicule/{id}/pdf', name: 'app_vehicule_pdf')]
public function generatePdf(Vehicule $vehicule): Response
{
    // Configuration de DomPDF
    
    $dompdf = new Dompdf();
    
    
    // Génération du HTML
    $html = $this->renderView('vehicule/pdf_template.html.twig', [
        'vehicule' => $vehicule
    ]);

    // Conversion en PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Création de la réponse
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="vehicule_'.$vehicule->getId().'.pdf"');

    return $response;
}

#[Route('/vehicule/search', name: 'app_vehicule_search', methods: ['GET'])]
public function search(Request $request, VehiculeRepository $vehiculeRepository): Response
{
    // Récupérer le terme de recherche depuis la requête
    $query = $request->query->get('q', '');

    // Rechercher les véhicules correspondants
    $vehicules = $vehiculeRepository->search($query);

    // Retourner la vue partielle avec les résultats
    return $this->render('vehicule/_vehicules.html.twig', [
        'vehicules' => $vehicules,
    ]);
}

// src/Controller/VehiculeController.php

#[Route('/vehicule/{id}/pin', name: 'app_vehicule_pin', methods: ['POST'])]
public function pin(Vehicule $vehicule, EntityManagerInterface $entityManager): Response
{
    // Inverser l'état isPinned
    $vehicule->setIsPinned(!$vehicule->isPinned());

    // Enregistrer les modifications
    $entityManager->flush();

    // Rediriger vers la liste des véhicules
    return $this->redirectToRoute('app_vehicule_index');
}
    
}