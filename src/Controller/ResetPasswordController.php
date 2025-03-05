<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends AbstractController
{
    // 1. Afficher le formulaire pour entrer le numTel
    #[Route('/password/request', name: 'password_request', methods: ['GET', 'POST'])]
    public function requestReset(Request $request, UserRepository $userRepository, SmsService $smsService, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $phoneNumber = $request->request->get('telephone');

            if (!$phoneNumber) {
                $this->addFlash('danger', 'Numéro de téléphone requis.');
                return $this->redirectToRoute('password_request');
            }

            $user = $userRepository->findOneBy(['telephone' => $phoneNumber]);

            if (!$user) {
                $this->addFlash('danger', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('password_request');
            }

            // Générer un code à 6 chiffres
            $resetCode = random_int(100000, 999999);
            $user->setResetCode((string) $resetCode);
            $em->persist($user);
            $em->flush();

            // Envoyer le code par SMS
            $smsService->sendSms($phoneNumber, (string) $resetCode);

            $this->addFlash('success', 'Code de réinitialisation envoyé par SMS.');

            return $this->render('security/verify_reset_code.html.twig', [
                'telephone' => $phoneNumber,
            ]);
        }

        return $this->render('security/request_reset_password.html.twig');
    }

    // 2. Vérification du code
    #[Route('/password/verify', name: 'password_verify', methods: ['POST'])]
    public function verifyCode(Request $request, UserRepository $userRepository): Response
    {
        $phoneNumber = $request->request->get('telephone');
        $code = $request->request->get('resetCode');

        if (!$phoneNumber || !$code) {
            $this->addFlash('danger', 'Données incomplètes.');
            return $this->redirectToRoute('password_request');
        }

        $user = $userRepository->findOneBy(['telephone' => $phoneNumber, 'resetCode' => $code]);

        if (!$user) {
            $this->addFlash('danger', 'Code incorrect.');
            return $this->redirectToRoute('password_request');
        }

        return $this->render('security/reset_password.html.twig', [
            'telephone' => $phoneNumber,
            'resetCode' => $code,
        ]);
    }

    // 3. Réinitialisation du mot de passe
    #[Route('/password/reset', name: 'password_reset', methods: ['POST'])]
    public function resetPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $phoneNumber = $request->request->get('telephone');
        $code = $request->request->get('resetCode');
        $newPassword = $request->request->get('newPassword');

        if (!$phoneNumber || !$code || !$newPassword) {
            $this->addFlash('danger', 'Données incomplètes.');
            return $this->redirectToRoute('password_request');
        }

        $user = $userRepository->findOneBy(['telephone' => $phoneNumber, 'resetCode' => $code]);

        if (!$user) {
            $this->addFlash('danger', 'Code incorrect.');
            return $this->redirectToRoute('password_request');
        }

        // Mettre à jour le mot de passe
        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
        $user->setResetCode(null);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Mot de passe réinitialisé avec succès. Connectez-vous avec votre nouveau mot de passe.');

        return $this->redirectToRoute('app_login'); // Redirection vers la page de connexion
    }
}