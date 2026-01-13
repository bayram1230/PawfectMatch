<?php

namespace App\Controller\Admin;

use App\Entity\Pet;
use App\Domain\Pets\PetStatus;
use App\Form\PetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/pets')]
final class AdminPetController extends AbstractController
{
    #[Route('/create', name: 'admin_pet_create')]
    public function create(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pet = new Pet();
        $pet->setStatus(PetStatus::ACTIVE);

        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pet);
            $em->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('pet/form.html.twig', [
            'form'  => $form->createView(),
            'title' => 'Create Pet (Admin)',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_pet_edit')]
    public function edit(
        Pet $pet,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('pet/form.html.twig', [
            'form'  => $form->createView(),
            'title' => 'Edit Pet (Admin)',
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_pet_delete', methods: ['POST'])]
    public function delete(
        Pet $pet,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Admin delete = archive (no hard delete)
        $pet->archive();
        $em->flush();

        return $this->redirectToRoute('admin_dashboard');
    }
}
