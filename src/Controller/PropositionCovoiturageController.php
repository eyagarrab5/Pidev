<?php

namespace App\Controller;
use App\Enum\StatutProposition;

use App\Entity\DemandeCovoiturage;
use App\Entity\PropositionCovoiturage;
use App\Form\PropositionCovoiturageType;
use App\Repository\PropositionCovoiturageRepository;
use App\Repository\DemandeCovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/proposition/covoiturage')]
final class PropositionCovoiturageController extends AbstractController
{
    #[Route('/admin/propositions', name: 'app_proposition_covoiturage_index', methods: ['GET'])]
    public function index(PropositionCovoiturageRepository $propositionCovoiturageRepository): Response
    {
        return $this->render('proposition_covoiturage/indexAdmin.html.twig', [
            'proposition_covoiturages' => $propositionCovoiturageRepository->findAll(),
        ]);
    }

    //ena zetha 
    #[Route('/listePropositionsRecues/{id}',name:'listePropositionsRecues', methods: ['GET'])]
    public function listePropositionsRecues($id,DemandeCovoiturageRepository $demandeCovoiturageRepository): Response
    {

        return $this->render('proposition_covoiturage/listePropositionsRecues.html.twig', [
            'demande_covoiturages' => $demandeCovoiturageRepository->findByPassager_Id($id),
        ]);
    }
    #[Route('/conducteursProposent/{id}', name: 'conducteursProposent', methods: ['GET'])]
public function conducteursProposent(int $id, PropositionCovoiturageRepository $propositionCovoiturageRepository,DemandeCovoiturageRepository $demandeCovoiturageRepository): Response
{
    // Find the associated 'Demande' or 'PropositionCovoiturage' by ID
    $demande = $demandeCovoiturageRepository->find($id); // Or findbyDemande if that's more relevant

    if (!$demande) {
        throw $this->createNotFoundException('La demande n\'a pas été trouvée.');
    }

    // Use the 'demande' or 'id' to fetch the related propositions
    $propositionCovoiturages = $propositionCovoiturageRepository->findByDemande($demande); // Assuming findByDemande method exists

    return $this->render('proposition_covoiturage/conducteursProposent.html.twig', [
        'proposition_covoiturages' => $propositionCovoiturages, // Pass data to the template
    ]);
}

#[Route('/mesPropositions/{id}',name:'mesPropositions', methods: ['GET'])]
public function listeMesPropositions($id,PropositionCovoiturageRepository $propositionCovoiturageRepository): Response
{

    return $this->render('proposition_covoiturage/mesPropositions.html.twig', [
        'proposition_covoiturages' => $propositionCovoiturageRepository->findByConducteur_Id($id),
    ]);
}


    #[Route('/new/{id}', name: 'app_proposition_covoiturage_new', methods: ['GET', 'POST'])]
    public function new($id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = $entityManager->getRepository(DemandeCovoiturage::class)->find($id);
        $propositionCovoiturage = new PropositionCovoiturage();
        $propositionCovoiturage->setConducteurId(123);
        $propositionCovoiturage->setStatut(StatutProposition::EN_ATTENTE);
        $propositionCovoiturage->setDemande($demande);

        $form = $this->createForm(PropositionCovoiturageType::class, $propositionCovoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($propositionCovoiturage);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_covoiturage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('proposition_covoiturage/new.html.twig', [
            'proposition_covoiturage' => $propositionCovoiturage,
            'form' => $form,
        ]);
    }
     
   

    #[Route('/{id}/edit', name: 'app_proposition_covoiturage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PropositionCovoiturage $propositionCovoiturage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PropositionCovoiturageType::class, $propositionCovoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('mesPropositions', ['id' => 123], Response::HTTP_SEE_OTHER);
        }

        return $this->render('proposition_covoiturage/edit.html.twig', [
            'proposition_covoiturage' => $propositionCovoiturage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_proposition_covoiturage_delete', methods: ['POST'])]
    public function delete(Request $request, PropositionCovoiturage $propositionCovoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propositionCovoiturage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($propositionCovoiturage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mesPropositions', ['id' => 123], Response::HTTP_SEE_OTHER);
    }
    #[Route('/admin/propositions/{id}', name: 'app_proposition_covoiturage_delete_Admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, PropositionCovoiturage $propositionCovoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$propositionCovoiturage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($propositionCovoiturage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_proposition_covoiturage_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
