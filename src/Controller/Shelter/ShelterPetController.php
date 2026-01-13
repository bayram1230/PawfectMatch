<?php

namespace App\Controller\Shelter;

use App\Entity\Pet;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shelter/pets')]
final class ShelterPetController extends AbstractController
{
    #[Route('', name: 'shelter_pets')]
    public function index(PetRepository $petRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SHELTER');

        $shelter = $this->getUser();
        if ($shelter === null) {
            throw $this->createAccessDeniedException();
        }

        $pets = $petRepository->findActiveByShelter($shelter);

        return $this->render('shelter/pet/index.html.twig', [
            'pets' => $pets,
        ]);
    }

    #[Route('/create', name: 'shelter_pet_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SHELTER');

        // ⛔ Noch kein Form – nur Platzhalter
        return $this->render('shelter/pet/create.html.twig');
    }

    #[Route('/{id}/edit', name: 'shelter_pet_edit')]
    public function edit(Pet $pet): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SHELTER');

        // Ownership-Check
        if ($pet->getShelter() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('shelter/pet/edit.html.twig', [
            'pet' => $pet,
        ]);
    }

    #[Route('/{id}/delete', name: 'shelter_pet_delete', methods: ['POST'])]
    public function delete(
        Pet $pet,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SHELTER');

        if ($pet->getShelter() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        /**
         * ❗ Business-Regel (später ausbauen):
         * - Pet darf nur gelöscht werden,
         *   wenn es KEINE AdoptionRequests hat
         */
        if ($pet->getAdoptionRequests()->count() > 0) {
            $this->addFlash('error', 'This pet cannot be deleted.');
            return $this->redirectToRoute('shelter_dashboard');
        }

        $em->remove($pet);
        $em->flush();

        return $this->redirectToRoute('shelter_dashboard');
    }
}
