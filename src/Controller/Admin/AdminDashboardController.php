<?php

namespace App\Controller\Admin;

use App\Repository\PetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function __invoke(PetRepository $petRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pets = $petRepository->findAllActive();

        return $this->render('dashboard/dashboard.html.twig', [
            'dashboard_title'  => 'Admin Dashboard',
            'pets'             => $pets,
            'can_manage_pets'  => true,
            'create_pet_route' => 'admin_pet_create',
            'edit_pet_route'   => 'admin_pet_edit',
            'delete_pet_route' => 'admin_pet_delete',
        ]);
    }
}
