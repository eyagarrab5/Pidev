<?php

namespace App\Controller;
use App\Enum\StatutOffre;
use App\Entity\OffreCovoiturage;
use App\Form\OffreCovoiturageType;
use App\Repository\OffreCovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre/covoiturage')]
final class OffreCovoiturageController extends AbstractController
{
    #[Route(name: 'app_offre_covoiturage_index', methods: ['GET'])]
    public function index(OffreCovoiturageRepository $offreCovoiturageRepository): Response
    {
        return $this->render('offre_covoiturage/index.html.twig', [
            'offre_covoiturages' => $offreCovoiturageRepository->findAll(),
        ]);
    }
    #[Route('/admin/offres', name: 'app_offre_covoiturage_index_Admin', methods: ['GET'])]
    public function indexAdmin(OffreCovoiturageRepository $offreCovoiturageRepository): Response
    {
        return $this->render('offre_covoiturage/indexAdmin.html.twig', [
            'offre_covoiturages' => $offreCovoiturageRepository->findAll(),
        ]);
    }
    #[Route( '/mesOffres/{id}' ,name: 'mesOffres', methods: ['GET'])]
    public function mesOffres(OffreCovoiturageRepository $offreCovoiturageRepository,$id): Response
    {
        return $this->render('offre_covoiturage/mesOffres.html.twig', [
            'offre_covoiturages' => $offreCovoiturageRepository->findById($id),
        ]);
    }

    #[Route('/new', name: 'app_offre_covoiturage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offreCovoiturage = new OffreCovoiturage();
        $offreCovoiturage->setConducteurId(123);
        $offreCovoiturage->setStatut(StatutOffre::EN_ATTENTE);
        $form = $this->createForm(OffreCovoiturageType::class, $offreCovoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offreCovoiturage);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_covoiturage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_covoiturage/new.html.twig', [
            'offre_covoiturage' => $offreCovoiturage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_covoiturage_show', methods: ['GET'])]
    public function show(OffreCovoiturage $offreCovoiturage): Response
    {
        return $this->render('offre_covoiturage/show.html.twig', [
            'offre_covoiturage' => $offreCovoiturage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_covoiturage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OffreCovoiturage $offreCovoiturage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreCovoiturageType::class, $offreCovoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('mesOffres', ['id' => 123], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_covoiturage/edit.html.twig', [
            'offre_covoiturage' => $offreCovoiturage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_covoiturage_delete', methods: ['POST'])]
    public function delete(Request $request, OffreCovoiturage $offreCovoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offreCovoiturage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offreCovoiturage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mesOffres', ['id' => 123], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/admin/offres/{id}', name: 'app_offre_covoiturage_delete_Admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, OffreCovoiturage $offreCovoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offreCovoiturage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offreCovoiturage);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_offre_covoiturage_index_Admin', [], Response::HTTP_SEE_OTHER);
    }
    

}
