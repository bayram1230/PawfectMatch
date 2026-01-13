<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Repository\PetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(PetRepository $petRepository): Response
    {
        // Daten für Homepage laden (Doctrine → MySQL intern)
        $pets = $petRepository->findForHomepage(12);

        return $this->render('home/index.html.twig', [
            'pets' => $pets,
        ]);
    }
}
