<?php

namespace App\Controller\User;

use App\Repository\AdoptionRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'user_dashboard')]
    public function __invoke(
        AdoptionRequestRepository $adoptionRequestRepository
    ): Response {
        // Defense in depth (access_control should already handle this)
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        if ($user === null) {
            throw $this->createAccessDeniedException();
        }

        /**
         * Read model only:
         * - count adoption requests created by this user
         * - no domain logic
         */
        $applicationsCount = $adoptionRequestRepository->countByApplicant($user);

        return $this->render('dashboard/user-dashboard.html.twig', [
            'username'            => $user->getUsername(),
            'applications_count'  => $applicationsCount,
        ]);
    }
}
