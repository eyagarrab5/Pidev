<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ProfileFormType;
use App\Form\PhotoFormType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class ProfileController extends AbstractController
{
    private $entityManager;

    // Inject EntityManagerInterface into the controller
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/profile', name: 'profile_index')]
public function index(Request $request): Response
{
    $user = $this->getUser();

    // Créez le formulaire d'édition du profil
    $profileForm = $this->createForm(ProfileFormType::class, $user);

    // Gestion de la soumission du formulaire
    $profileForm->handleRequest($request);
    if ($profileForm->isSubmitted() && $profileForm->isValid()) {
        // Sauvegarde les modifications du profil
        $this->entityManager->flush();

        // Message flash pour informer l'utilisateur
        $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');

        // Redirige vers la même page pour éviter la soumission multiple du formulaire
        return $this->redirectToRoute('profile_index');
    }

    // Rendu du template avec le formulaire et les données de l'utilisateur
    return $this->render('profile/index.html.twig', [
        'user' => $user,
        'profileForm' => $profileForm->createView(),
    ]);
}
}