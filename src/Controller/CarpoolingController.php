<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CarpoolingController extends AbstractController
{
    #[Route('/carpooling', name: 'app_carpooling')]
    public function index(): Response
    {
        return $this->render('carpooling/index.html.twig', [
            'controller_name' => 'CarpoolingController',
            'title' => 'Carpooling',
        ]);
    }
}