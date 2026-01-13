<?php

namespace App\Controller\Shelter;

use App\Repository\PetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shelter')]
final class ShelterDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'shelter_dashboard')]
    public function __invoke(PetRepository $petRepository): Response
    {
        // Defense in depth (access_control should already enforce this)
        $this->denyAccessUnlessGranted('ROLE_SHELTER');

        $shelterUser = $this->getUser();

        if ($shelterUser === null) {
            throw $this->createAccessDeniedException();
        }

        /**
         * Read model only:
         * - only ACTIVE pets
         * - only pets belonging to this shelter
         * - no business logic here
         */
        $pets = $petRepository->findActiveByShelter($shelterUser);

        return $this->render('dashboard/dashboard.html.twig', [
            'dashboard_title'  => 'Shelter Dashboard',
            'pets'             => $pets,
            'can_manage_pets'  => true,
            'create_pet_route' => 'shelter_pet_create',
            'edit_pet_route'   => 'shelter_pet_edit',
            'delete_pet_route' => 'shelter_pet_delete',
        ]);
    }
}
