<?php

namespace App\Controller;
use App\Entity\Vehicule;
use App\Entity\ReservationVehicule;
use App\Form\ReservationVehiculeType;
use App\Repository\ReservationVehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/res')]
final class ResController extends AbstractController
{
    #[Route(name: 'app_res_index', methods: ['GET'])]
    public function index(ReservationVehiculeRepository $reservationVehiculeRepository): Response
    {
        return $this->render('res/index.html.twig', [
            'reservation_vehicules' => $reservationVehiculeRepository->findAll(),
        ]);
    }
    #[Route('/n1',name: 'app_res_index2', methods: ['GET'])]
    public function index5(ReservationVehiculeRepository $reservationVehiculeRepository): Response
    {
        return $this->render('res/index2.html.twig', [
            'reservation_vehicules' => $reservationVehiculeRepository->findAll(),
        ]);
    }

    #[Route('/new/{vehiculeId}', name: 'app_res_new', methods: ['GET', 'POST'])]
    public function new3(Request $request, EntityManagerInterface $entityManager, ?int $vehiculeId): Response
    {
        // Find the vehicle by ID
        $vehicule = $entityManager->getRepository(Vehicule::class)->find($vehiculeId);
    
        // If the vehicle is not found, throw a 404 error
        if (!$vehicule) {
            throw $this->createNotFoundException('Véhicule non trouvé.');
        }
    
        // Create a new reservation and associate it with the vehicle
        $reservationVehicule = new ReservationVehicule();
        $reservationVehicule->setIdVehicule($vehicule); // Pass the Vehicule object, not the ID
    
        // Create the form
        $form = $this->createForm(ReservationVehiculeType::class, $reservationVehicule);
        $form->handleRequest($request);
    
        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the reservation to the database
            $entityManager->persist($reservationVehicule);
            $entityManager->flush();
    
            // Redirect to the reservation list page
            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }
    
        // Render the form
        return $this->render('res/new.html.twig', [
            'reservation_vehicule' => $reservationVehicule,
            'form' => $form->createView(),
        ]);
       
    }



    #[Route('/new', name: 'app_res_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservationVehicule = new ReservationVehicule();
        $form = $this->createForm(ReservationVehiculeType::class, $reservationVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservationVehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('res/new.html.twig', [
            'reservation_vehicule' => $reservationVehicule,
            'form' => $form,
        ]);
    }

    #[Route('/n2', name: 'app_res_new5', methods: ['GET', 'POST'])]
    public function new5(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservationVehicule = new ReservationVehicule();
        $form = $this->createForm(ReservationVehiculeType::class, $reservationVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservationVehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_res_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('res/new2.html.twig', [
            'reservation_vehicule' => $reservationVehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_res_show', methods: ['GET'])]
    public function show(ReservationVehicule $reservationVehicule): Response
    {
        return $this->render('res/show.html.twig', [
            'reservation_vehicule' => $reservationVehicule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_res_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReservationVehicule $reservationVehicule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationVehiculeType::class, $reservationVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_res_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('res/edit.html.twig', [
            'reservation_vehicule' => $reservationVehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_res_delete', methods: ['POST'])]
    public function delete(Request $request, ReservationVehicule $reservationVehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservationVehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservationVehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_res_index', [], Response::HTTP_SEE_OTHER);
    }
}
