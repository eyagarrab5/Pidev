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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


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
public function new(
    Request $request, 
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger,
    #[Autowire('%kernel.project_dir%/public/uploads/brochures')] string $brochuresDirectory,
    #[Autowire('%env(FACEBOOK_PAGE_ID)%')] string $facebookPageId,
    #[Autowire('%env(FACEBOOK_ACCESS_TOKEN)%')] string $accessToken
): Response
{
    $offreCovoiturage = new OffreCovoiturage();
    $offreCovoiturage->setConducteurId(123);
    $offreCovoiturage->setStatut(StatutOffre::EN_ATTENTE);
    $form = $this->createForm(OffreCovoiturageType::class, $offreCovoiturage);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle file upload
        $brochureFile = $form->get('brochure')->getData();

        if ($brochureFile) {
            $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

            try {
                $brochureFile->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'An error occurred while uploading the file.');
                return $this->redirectToRoute('app_offre_covoiturage_new');
            }

            $offreCovoiturage->setImg($newFilename);
        }

        // Save the entity to the database
        $entityManager->persist($offreCovoiturage);
        $entityManager->flush();

        // Partager sur Facebook
        try {
            $client = HttpClient::create();
            
            $postMessage = sprintf(
                "🚗 Nouvelle offre de covoiturage disponible !\n\n".
                "📍 Départ : %s\n".
                "🏁 Destination : %s\n".
                "📅 Date : %s\n".
                "💵 Prix : %s DT\n".
                " Matricule : %s\n",

                $offreCovoiturage->getDepart(),
                $offreCovoiturage->getDestination(),
                $offreCovoiturage->getDate()->format('Y-m-d H:i'),
                $offreCovoiturage->getPrix(),
                $offreCovoiturage->getMatVehicule()
            );

            $response = $client->request('POST', "https://graph.facebook.com/v22.0/{$facebookPageId}/feed", [
                'query' => [
                    'message' => $postMessage,
                    'access_token' => $accessToken
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray();

            if ($statusCode !== 200 || isset($content['error'])) {
                $this->addFlash('warning', 'L\'offre de covoiturage a été créée mais le partage Facebook a échoué');
            } else {
                $this->addFlash('success', 'L\'offre de covoiturage a été créée et partagée sur Facebook !');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du partage Facebook : '.$e->getMessage());
        }

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
