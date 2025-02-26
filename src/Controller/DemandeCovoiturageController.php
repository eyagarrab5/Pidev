<?php

namespace App\Controller;
use App\Enum\StatutDemande;
use App\Entity\DemandeCovoiturage;
use App\Form\DemandeCovoiturageType;
use App\Repository\DemandeCovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/demande/covoiturage')]
final class DemandeCovoiturageController extends AbstractController
{
    #[Route(name: 'app_demande_covoiturage_index', methods: ['GET'])]
    public function index(DemandeCovoiturageRepository $demandeCovoiturageRepository): Response
    {
        return $this->render('demande_covoiturage/index.html.twig', [
            'demande_covoiturages' => $demandeCovoiturageRepository->findAll(),
        ]);
    }
    
#[Route('/admin/demandes', name: 'app_demande_covoiturage_index_Admin', methods: ['GET'])]
public function indexAdmin(DemandeCovoiturageRepository $demandeCovoiturageRepository): Response
{
    return $this->render('demande_covoiturage/indexAdmin.html.twig', [
        'demande_covoiturages' => $demandeCovoiturageRepository->findAll(),
    ]);
}

    //ena zetha 
    #[Route('/mesDemandes/{id}',name: 'mesDemandes', methods: ['GET'])]
    public function mesDemandes(DemandeCovoiturageRepository $demandeCovoiturageRepository,$id): Response
    {
        return $this->render('demande_covoiturage/mesDemandes.html.twig', [
            'demande_covoiturages' => $demandeCovoiturageRepository->findById($id),
        ]);
    }

    #[Route('/new', name: 'app_demande_covoiturage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demandeCovoiturage = new DemandeCovoiturage();
        $demandeCovoiturage->setPassagerId(456);
        $demandeCovoiturage->setStatut(StatutDemande::EN_ATTENTE);
        $form = $this->createForm(DemandeCovoiturageType::class, $demandeCovoiturage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demandeCovoiturage);
            $entityManager->flush();
            return $this->redirectToRoute('app_demande_covoiturage_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('demande_covoiturage/new.html.twig', [
            'demande_covoiturage' => $demandeCovoiturage,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_demande_covoiturage_show', methods: ['GET'])]
    public function show(DemandeCovoiturage $demandeCovoiturage): Response
    {
        return $this->render('demande_covoiturage/show.html.twig', [
            'demande_covoiturage' => $demandeCovoiturage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_covoiturage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DemandeCovoiturage $demandeCovoiturage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeCovoiturageType::class, $demandeCovoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('mesDemandes', ['id' => 456], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande_covoiturage/edit.html.twig', [
            'demande_covoiturage' => $demandeCovoiturage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_covoiturage_delete', methods: ['POST'])]
    public function delete(Request $request, DemandeCovoiturage $demandeCovoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demandeCovoiturage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($demandeCovoiturage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mesDemandes', ["id"=>456], Response::HTTP_SEE_OTHER);
    }
    #[Route('/admin/demandes/{id}', name: 'app_demande_covoiturage_delete_Admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, DemandeCovoiturage $demandeCovoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $demandeCovoiturage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($demandeCovoiturage);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_demande_covoiturage_index_Admin', [], Response::HTTP_SEE_OTHER);
    }
   
}
