<?php

namespace App\Controller;
use App\Entity\OffreCovoiturage;
use App\Enum\StatutReservation;
use App\Repository\OffreCovoiturageRepository;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route('/admin/reservations', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/indexAdmin.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/listeReservationsRecues/{id}',name:'listeReservationsRecues', methods: ['GET'])]
    public function listeReservationsRecues($id,OffreCovoiturageRepository $offreCovoiturageRepository): Response
    {

        return $this->render('reservation/listeReservationsRecues.html.twig', [
            'offre_covoiturages' => $offreCovoiturageRepository->findByConducteur_Id($id),
        ]);
    }
   
    #[Route('/passagersReservent/{id}', name: 'passagersReservent', methods: ['GET'])]
    public function passagersReservent(int $id, ReservationRepository $reservationRepository,OffreCovoiturageRepository $offreCovoiturageRepository): Response
    {
        // Find the associated 'Demande' or 'PropositionCovoiturage' by ID
        $offre = $offreCovoiturageRepository->find($id); // Or findbyoffre if that's more relevant
    
        if (!$offre) {
            throw $this->createNotFoundException('La offre n\'a pas été trouvée.');
        }
    
        // Use the 'offre' or 'id' to fetch the related propositions
        $reservation = $reservationRepository->findByOffre($offre); // Assuming findByDemande method exists
    
        return $this->render('reservation/passagersReservent.html.twig', [
            'reservations' => $reservation, // Pass data to the template
        ]);
    }
    #[Route('/mesReservations/{id}',name:'mesReservations', methods: ['GET'])]
       public function listeMesReservations($id,ReservationRepository $reservationRepository): Response
    {

    return $this->render('reservation/mesReservation.html.twig', [
        'reservations' => $reservationRepository->findByPassager_Id($id),
    ]);
    }

  /*
#[Route('/new/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, $id): Response
{
    $offre = $entityManager->getRepository(OffreCovoiturage::class)->find($id);
    $reservation = new Reservation();
    $reservation->setPassagerId(456);
    $reservation->setStatut(StatutReservation::EN_ATTENTE);
    $reservation->setOffre($offre);

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('app_offre_covoiturage_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('reservation/new.html.twig', [
        'reservation' => $reservation,
        'form' => $form,
    ]);
}
*/
//ki yreservi tet3ada toul menghyr form
    #[Route('/new/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, $id): Response
{
    // Récupérer l'offre de covoiturage à partir de l'ID
    $offre = $entityManager->getRepository(OffreCovoiturage::class)->find($id);

    if (!$offre) {
        throw $this->createNotFoundException('Offre de covoiturage non trouvée.');
    }

    // Créer la nouvelle réservation
    $reservation = new Reservation();
    $reservation->setPassagerId(456); // Passager ID que tu veux définir
    $reservation->setStatut(StatutReservation::EN_ATTENTE); // Statut initial
    $reservation->setOffre($offre);

    // Persister la réservation directement sans passer par le formulaire
    $entityManager->persist($reservation);
    $entityManager->flush();

    // Rediriger vers une autre page après la création de la réservation
    return $this->redirectToRoute('app_offre_covoiturage_index', [], Response::HTTP_SEE_OTHER);
}


    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mesReservations', ['id' => 456], Response::HTTP_SEE_OTHER);
    }
   
    #[Route('/admin/reservations/{id}', name: 'app_reservation_delete_Admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
        $entityManager->remove($reservation);
        $entityManager->flush();
    }
    return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    
}

}
