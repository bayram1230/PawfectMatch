<?php

namespace App\Controller\Admin;

use App\Entity\AdoptionHistory;
use App\Repository\AdoptionRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/adoption-requests')]
class AdminAdoptionRequestController extends AbstractController
{
    #[Route('', name: 'admin_adoption_request_list')]
    public function list(AdoptionRequestRepository $repository): Response
    {
        return $this->render('admin/adoption_request/list.html.twig', [
            'requests' => $repository->findAll(),
        ]);
    }

    #[Route('/{id}/approve', name: 'admin_adoption_request_approve', methods: ['POST'])]
    public function approve(
        int $id,
        AdoptionRequestRepository $repository,
        EntityManagerInterface $entityManager
    ): Response {
        $request = $repository->find($id);

        if (!$request) {
            throw $this->createNotFoundException();
        }

        $history = new AdoptionHistory();
        $history->setAdoptionRequest($request);
        $history->setStatus('Approved');
        $history->setDecidedAt(new \DateTimeImmutable());
        $history->setDecidedBy($this->getUser());

        $entityManager->persist($history);
        $entityManager->flush();

        return $this->redirectToRoute('admin_adoption_request_list');
    }

    #[Route('/{id}/reject', name: 'admin_adoption_request_reject', methods: ['POST'])]
    public function reject(
        int $id,
        AdoptionRequestRepository $repository,
        EntityManagerInterface $entityManager
    ): Response {
        $request = $repository->find($id);

        if (!$request) {
            throw $this->createNotFoundException();
        }

        $history = new AdoptionHistory();
        $history->setAdoptionRequest($request);
        $history->setStatus('Rejected');
        $history->setDecidedAt(new \DateTimeImmutable());
        $history->setDecidedBy($this->getUser());

        $entityManager->persist($history);
        $entityManager->flush();

        return $this->redirectToRoute('admin_adoption_request_list');
    }
}
