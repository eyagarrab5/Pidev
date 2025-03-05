<?php
// src/Controller/UserController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\BanUserType;


#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response

    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}



/*<?php
namespace App\Controller;

use App\Form\UserFormType;
use App\Form\BanUserType;
use App\Form\ProfilType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Service\GeocodingService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
//use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
//use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Form\ChangerMDPType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Security\AppAuthenticator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserController extends AbstractController
{
    #[Route('/admin/userList', name: 'userList')]
    public function userList(ManagerRegistry $mr, AuthorizationCheckerInterface $authChecker): Response
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            // Redirect to login page if not an admin
            return $this->redirectToRoute('app_login');
        }
        $manager = $mr->getManager();
        $repo = $manager->getRepository(User::class);
        $list = $repo->findAll();
        return $this->render('user/userlist.html.twig', [
            'userList' => $list,
        ]);
    }

    #[Route('/login/addUser', name: 'insertUser')]
public function insertUser(Request $request, ManagerRegistry $mr, UserPasswordHasherInterface $passwordHasher): Response
{
    $manager = $mr->getManager(); 
    $user = new \App\Entity\User();
    $form = $this->createForm(UserFormType::class, $user);
    $form->handleRequest($request);

    // Vérifier si le formulaire est soumis
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer la réponse reCAPTCHA
        $recaptchaResponse = $request->request->get('g-recaptcha-response');

        // Vérifier si le reCAPTCHA est coché
        if (!$recaptchaResponse) {
            $this->addFlash('error', 'Veuillez cocher le reCAPTCHA.');
            return $this->redirectToRoute('insertUser');
        }

        // Valider le reCAPTCHA avec l'API de Google
        $secretKey = '6LcCbOcqAAAAAHdRi5G4fwmRRGKuT5WwmpMJrPZ-'; // Remplacez par votre clé secrète
        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
        $responseData = json_decode($verifyResponse);

        // Vérifier si la réponse reCAPTCHA est valide
        if (!$responseData->success) {
            $this->addFlash('error', 'Échec de la vérification du reCAPTCHA. Veuillez réessayer.');
            return $this->redirectToRoute('insertUser');
        }

        // Continuer avec la création de l'utilisateur
        $plainPassword = $form->get('plainPassword')->getData();
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Gérer l'image de l'utilisateur
        $photoFile = $form->get('userPicture')->getData();
        if (!$photoFile) {
            $avatarPath = $this->generateAvatar($user);
            $user->setUserPicture($avatarPath);
        }

        if ($photoFile) {
            $newFilename = uniqid() . '.' . $photoFile->guessExtension();
            try {
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/assets/images/',
                    $newFilename
                );
                $user->setUserPicture('assets/images/' . $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', "Erreur lors du chargement de l'image.");
            }
        }

        // Définir le rôle de l'utilisateur
        if ($user->getUserRole() == "Medecin") {
            $user->setRoles(['ROLE_USER', 'ROLE_MEDECIN']);
        } elseif ($user->getUserRole() == "Patient") {
            $user->setRoles(['ROLE_USER', 'ROLE_PATIENT']);
        }

        // Persister l'utilisateur
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'Utilisateur créé avec succès.');
        return $this->redirectToRoute("app_login");
    }

    return $this->render("user/formadduser.html.twig", [
        "form" => $form
    ]);
}

    
    #[Route('/deleteUser/{id}', name: 'deleteUser')]
    public function deleteUser(EntityManagerInterface $em, UserRepository $repo, $id): Response
    {
        $user = $repo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute("userList");
    }

    #[Route('/admin/updateAdmin/{id}', name: 'updateAdmin')]
    public function updateAdmin(Request $request, ManagerRegistry $mr, $id, UserRepository $repo): Response
    {
        $manager = $mr->getManager();
        $user = new \App\Entity\User();
        $user=$repo->find($id);
        $originalRole = $user->getUserRole();
        $originalMDP=$user->getPassword();
        $originalStatut=$user->isStatutCompte();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('userPicture')->getData();

            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/assets/images/',
                        $newFilename
                    );
                    $user->setUserPicture('assets/images/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors du chargement de l'image.");
                }
            }
            //$user->setRoles(['ROLE_USER']);
            $user->setUserRole($originalRole);
            $user->setPassword($originalMDP);
            $user->setStatutCompte($originalStatut);

            if ($user->getUserRole() !== "Medecin") {
                $user->setDocSpecialty(null);
            }
            if ($user->getUserRole() == "Medecin") {
                $user->setRoles(['ROLE_USER','ROLE_MEDECIN']);
            }
            if ($user->getUserRole() == "Patient") {
                $user->setRoles(['ROLE_USER','ROLE_PATIENT']);
            }
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');
            return $this->redirectToRoute('userList');
        }
        return $this->render("user/updateAdmin.html.twig", [
            'user' => $user,
            "form" => $form
        ]);
    }

    #[Route('/profil/updateUser/{id}', name: 'updateUser')]
public function updateUser(Request $request, ManagerRegistry $mr, $id, UserRepository $repo): Response
{
    $manager = $mr->getManager();
    $user = $repo->find($id);
    $originalRole = $user->getUserRole();
    $originalPic = $user->getUserPicture();
    if (!$user instanceof \App\Entity\User) {
        throw $this->createAccessDeniedException('Utilisateur non authentifié.');
    }

    // Récupérer le profil de l'utilisateur
    $profil = $user->getProfil();
    if (!$profil) {
        throw $this->createAccessDeniedException('Aucun profil trouvé pour cet utilisateur.');
    }
    $originalRol = $profil->getUserRole();

    // Créer le formulaire pour modifier le profil
    $form = $this->createForm(ProfilType::class, $profil);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Traitement de l'image (pour l'utilisateur)
        $photoFile = $form->get('userPicture')->getData();
        if ($photoFile) {
            // Si une nouvelle image est téléchargée
            $newFilename = uniqid() . '.' . $photoFile->guessExtension();
            try {
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/assets/images/',
                    $newFilename
                );
                $user->setUserPicture('assets/images/' . $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', "Erreur lors du chargement de l'image.");
            }
        } else {
            // Si aucune nouvelle image n'est téléchargée, on garde l'ancienne
            $user->setUserPicture($originalPic);
        }

        // Si le rôle n'est pas "Medecin", on supprime la spécialité
        if ($user->getUserRole() !== "Medecin" || $profil->getUserRole() !== "Medecin") {
            $user->setDocSpecialty(null);
            $profil->setDocSpecialty(null);
        }

        // On ne modifie pas le userRole ici
        $user->setUserRole($originalRole); // Garder l'ancien rôle

        // Mise à jour des informations du profil
        $user->setFirstName($profil->getFirstName());
        $user->setLastName($profil->getLastName());
        $user->setUserAge($profil->getUserAge());
        $profil->setUserRole($originalRol);
        

        // Persister et flush
        $manager->persist($profil); // Persister les changements du profil
        $manager->persist($user);   // Persister les changements de l'utilisateur
        $manager->flush();

        // Afficher un message de succès et rediriger l'utilisateur
        $this->addFlash('success', 'Profil mis à jour avec succès.');
        return $this->redirectToRoute('userProfile', ['id' => $user->getId()]);
    }

    // Rendu du formulaire
    return $this->render("user/formupdateUser.html.twig", [
        'user' => $user,
        "form" => $form->createView()
    ]);
}


    #[Route('/profil/desactiver/{id}', name: 'desactiver')]
    public function desactiverCompte($id, UserRepository $repo): Response
    {
        $user = $repo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }
        
        $user->setStatutCompte(false);

        $this->addFlash('success', 'Compte désactivé avec succès.');
        return $this->redirectToRoute("app_logout");
    }

    #[Route('/profil/profile/{id}', name: 'userProfile')]
public function userProfile(int $id, EntityManagerInterface $em, GeocodingService $geocodingService): Response
{
    // Récupérer l'utilisateur depuis la base de données
    $user = $em->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Récupérer les coordonnées de l'utilisateur
    $coordinates = $geocodingService->getCoordinates($user); // Passer l'objet User

    return $this->render('user/userProfile.html.twig', [
        'user' => $user,
        'latitude' => $coordinates['latitude'] ?? null,
        'longitude' => $coordinates['longitude'] ?? null,
    ]);
}

    #[Route('/admin/adminProfile', name: 'adminProfile')]
    public function showAdminProfile(UserRepository $repo): Response
    {
        $admin = $repo->find(1);
        if (!$admin) {
            throw $this->createNotFoundException('Admin introuvable.');
        }
        return $this->render('user/adminProfile.html.twig', [
            'user' => $admin
        ]);
    }

    #[Route('/password_change', name: 'change_password', methods: ['GET', 'POST'])]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user instanceof \App\Entity\User) {
        throw new \LogicException('Erreur!');
    }

    if ($request->isMethod('POST')) {
        $oldPassword = $request->request->get('oldPassword');
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');

        // Vérifier si l'ancien mot de passe est correct
        if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
            $this->addFlash('danger', 'L\'ancien mot de passe est incorrect.');
            return $this->redirectToRoute('change_password');
        }

        // Vérifier si les nouveaux mots de passe correspondent
        if ($newPassword !== $confirmPassword) {
            $this->addFlash('danger', 'Les nouveaux mots de passe ne correspondent pas.');
            return $this->redirectToRoute('change_password');
        }

        // Mettre à jour le mot de passe
        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Mot de passe modifié avec succès.');
        return $this->redirectToRoute('userProfile', ['id' => $user->getId()]);
    }

    return $this->render('security/change_password.html.twig');
}

private function generateAvatar(User $user): string
{
    // Obtenir l'email de l'utilisateur et en faire un hachage
    $emailHash = md5(strtolower(trim($user->getuserEmail()))); // Hachage de l'email
    $avatarSize = 100;

    // Générer des couleurs de base à partir du hachage
    $bgColor = [
        hexdec(substr($emailHash, 0, 2)), 
        hexdec(substr($emailHash, 2, 2)), 
        hexdec(substr($emailHash, 4, 2))
    ];
    $eyeColor = [
        hexdec(substr($emailHash, 6, 2)), 
        hexdec(substr($emailHash, 8, 2)), 
        hexdec(substr($emailHash, 10, 2))
    ];
    $mouthColor = [
        hexdec(substr($emailHash, 12, 2)), 
        hexdec(substr($emailHash, 14, 2)), 
        hexdec(substr($emailHash, 16, 2))
    ];

    // Créer l'image de l'avatar
    $image = imagecreatetruecolor($avatarSize, $avatarSize);
    $backgroundColor = imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]);
    imagefill($image, 0, 0, $backgroundColor);

    // Dessiner un visage simple (un cercle pour la tête)
    $headColor = imagecolorallocate($image, 255, 220, 185); // Couleur de peau
    imagefilledellipse($image, $avatarSize / 2, $avatarSize / 2, $avatarSize - 20, $avatarSize - 20, $headColor);

    // Dessiner les yeux (deux cercles)
    $eyeRadius = 10;
    $eyeX = $avatarSize / 3;
    $eyeY = $avatarSize / 3;
    $eyeColor = imagecolorallocate($image, $eyeColor[0], $eyeColor[1], $eyeColor[2]);
    imagefilledellipse($image, $eyeX, $eyeY, $eyeRadius, $eyeRadius, $eyeColor); // œil gauche
    imagefilledellipse($image, $avatarSize - $eyeX, $eyeY, $eyeRadius, $eyeRadius, $eyeColor); // œil droit

    // Dessiner la bouche (un arc de cercle)
    $mouthColor = imagecolorallocate($image, $mouthColor[0], $mouthColor[1], $mouthColor[2]);
    imagearc($image, $avatarSize / 2, $avatarSize - 30, 40, 20, 0, 180, $mouthColor); // bouche simple

    // Sauvegarder l'avatar généré
    $avatarFilename = uniqid() . '.png';
    $avatarPath = $this->getParameter('kernel.project_dir') . '/public/avatars/' . $avatarFilename;

    // Sauvegarder l'image dans un fichier
    imagepng($image, $avatarPath);
    imagedestroy($image);

    return 'avatars/' . $avatarFilename;
}

#[Route('/admin/ban/{id}', name: 'admin_ban_user')]
public function banUser(Request $request, User $user, EntityManagerInterface $em): Response
{
    // Vérifier que l'utilisateur connecté est un administrateur
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    // Définir la date de bannissement (par exemple, 7 jours à partir de maintenant)
    $bannedUntil = new \DateTime('+3 minutes');
    $user->setBannedUntil($bannedUntil);

    // Enregistrer les modifications
    $em->persist($user);
    $em->flush();

    $this->addFlash('success', 'L\'utilisateur a été banni avec succès.');
    return $this->redirectToRoute('adminProfile'); // Rediriger vers le tableau de bord admin
}

#[Route('/user/map/{id}', name: 'user_map')]
public function showMap(int $id, GeocodingService $geocodingService, EntityManagerInterface $em): Response
{
    // Récupérer l'utilisateur depuis la base de données en utilisant EntityManager
    $user = $em->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Récupérer les coordonnées à partir de l'adresse
    $coordinates = $geocodingService->getCoordinates($user); // Passer l'objet User

    if (!$coordinates) {
        $this->addFlash('error', 'Impossible de localiser l\'adresse.');
        return $this->redirectToRoute('userProfile', ['id' => $id]);
    }

    return $this->render('user/map.html.twig', [
        'user' => $user,
        'latitude' => $coordinates['latitude'],
        'longitude' => $coordinates['longitude'],
    ]);
}

}*/
/*#[Route('/admin/ban/{id}', name: 'admin_ban_user')]
public function banUser(Request $request, User $user, EntityManagerInterface $em): Response
{
    // Vérifier que l'utilisateur connecté est un administrateur
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    // Définir la date de bannissement (par exemple, 7 jours à partir de maintenant)
    $bannedUntil = new \DateTime('+3 minutes');
    $user->setBannedUntil($bannedUntil);

    // Enregistrer les modifications
    $em->persist($user);
    $em->flush();

    $this->addFlash('success', 'L\'utilisateur a été banni avec succès.');
    return $this->redirectToRoute('adminProfile'); // Rediriger vers le tableau de bord admin
}*/
?>