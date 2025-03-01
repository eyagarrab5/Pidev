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
    // Créez une requête pour récupérer tous les véhicules
    $query = $vehiculeRepository->createQueryBuilder('v')->getQuery();

    // Paginez les résultats
    $vehicules = $paginator->paginate(
        $query, // Requête à paginer
        $request->query->getInt('page', 1), // Numéro de page par défaut
        6 // Nombre d'éléments par page
    );

    // Passez les résultats paginés au template
    return $this->render('vehicule/index.html.twig', [
        'vehicules' => $vehicules, // Utilisez $vehicules (objet paginé) ici
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

    
}