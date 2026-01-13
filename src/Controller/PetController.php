<?php

namespace App\Controller;

use App\Service\PetCatalog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles all public pet-related pages.
 *
 * This controller contains no business logic.
 * All decisions are delegated to application services.
 */
class PetController extends AbstractController
{
    public function __construct(
        private PetCatalog $petCatalog
    ) {
    }

    /**
     * List all pets.
     */
    #[Route('/pets', name: 'pets_list')]
    public function list(): Response
    {
        return $this->render('pets/list.html.twig', [
            'pets' => $this->petCatalog->getAll(),
        ]);
    }

    /**
     * Fostered pets page.
     *
     * Specific routes must come before the generic "{id}" route.
     */
    #[Route('/pets/fostered', name: 'pets_fostered')]
    public function fostered(): Response
    {
        return $this->render('pets/fostered.html.twig', [
            'pets' => $this->petCatalog->getFosteredPets(),
        ]);
    }

    /**
     * Adopted pets page.
     */
    #[Route('/pets/adopted', name: 'pets_adopted')]
    public function adopted(): Response
    {
        return $this->render('pets/adopted.html.twig', [
            'pets' => $this->petCatalog->getAdoptedPets(),
        ]);
    }

    /**
     * Pet of the week page.
     */
    #[Route('/pets/pet-of-week', name: 'pets_pet_of_week')]
    public function petOfWeek(): Response
    {
        return $this->render('pets/petOfWeek.html.twig', [
            'pet' => $this->petCatalog->getPetOfWeek(),
        ]);
    }

    /**
     * Pet matchmaker page (scoring-based, explainable).
     */
    #[Route('/pets/matchmaker', name: 'pets_matchmaker')]
public function matchmaker(Request $request): Response
{
    $result = null;
    $message = null;

    if ($request->isMethod('POST')) {
        $activity   = $request->request->get('activity');
        $home       = $request->request->get('home');
        $experience = $request->request->get('experience');
        $petType    = $request->request->get('petType');
        $agePref    = $request->request->get('agePref');

        if (
            !$activity &&
            !$home &&
            !$experience &&
            !$petType &&
            !$agePref
        ) {
            $message = 'Please select at least one option before finding your match.';
        } else {
            $result = $this->petCatalog->findMatch(
                $activity,
                $home,
                $experience,
                $petType,
                $agePref
            );

            if (!$result) {
                $message = 'Sorry, no pets currently match your criteria.';
            }
        }
    }

    return $this->render('pets/matchmaker.html.twig', [
        'result'  => $result,
        'message' => $message,
    ]);
}


    /**
     * Generic route for a single pet.
     *
     * Must be last and restricted to numeric IDs.
     */
    #[Route('/pets/{id}', name: 'pets_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id): Response
    {
        $pet = $this->petCatalog->getById($id);

        if (!$pet) {
            throw $this->createNotFoundException('Pet not found');
        }

        return $this->render('pets/detail.html.twig', [
            'pet' => $pet,
        ]);
    }
}
