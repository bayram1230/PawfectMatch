<?php

namespace App\Controller;

use App\Application\Adoption\CreateAdoptionRequest;
use App\Entity\AdoptionRequest;
use App\Form\AdoptionRequestType;
use App\Repository\PetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdoptionRequestController extends AbstractController
{
    #[Route('/pets/{id}/adopt', name: 'adoption_request_create')]
    public function create(
        int $id,
        Request $request,
        PetRepository $petRepository,
        CreateAdoptionRequest $createAdoptionRequest
    ): Response {
        $pet = $petRepository->find($id);

        if (!$pet) {
            throw $this->createNotFoundException('Pet not found');
        }

        $adoptionRequest = new AdoptionRequest();
        $form = $this->createForm(AdoptionRequestType::class, $adoptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $createAdoptionRequest->create(
                pet: $pet,
                fullName: $adoptionRequest->getFullName(),
                email: $adoptionRequest->getEmail(),
                message: $adoptionRequest->getMessage(),
                username: $this->getUser()?->getUserIdentifier() ?? 'guest',
                surveyAnswer: $adoptionRequest->getSurveyAnswer()
            );

            return $this->redirectToRoute('pet_detail', [
                'id' => $pet->getId(),
            ]);
        }

        return $this->render('adoption_request/create.html.twig', [
            'pet' => $pet,
            'form' => $form->createView(),
        ]);
    }
}
