<?php

namespace App\Controller\User;

use App\Repository\AdoptionRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserApplicationsController extends AbstractController
{
    #[Route('/applications', name: 'user_applications')]
    public function __invoke(
        AdoptionRequestRepository $adoptionRequestRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        if ($user === null) {
            throw $this->createAccessDeniedException();
        }

        /**
         * Read model:
         * - all adoption requests of this user
         */
        $applications = $adoptionRequestRepository->findByApplicant($user);

        return $this->render('user/applications.html.twig', [
            'applications' => $applications,
        ]);
    }
}
