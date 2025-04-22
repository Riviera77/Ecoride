<?php

namespace App\Controller;

use App\Repository\CarpoolingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carpooling', name: 'carpooling_')]
final class CarpoolingController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('carpooling/index.html.twig', [
            'title' => 'Carpooling',
        ]);
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(CarpoolingRepository $carpooling): Response
    {
        //dd(__METHOD__);
        //dd($carpooling->findAll());
        return $this->render('carpooling/list.html.twig', [
            'title' => 'Carpooling',
            'carpoolings' => $carpooling->findAll(),
        ]);
    }
}