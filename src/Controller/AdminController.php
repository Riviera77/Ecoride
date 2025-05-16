<?php

namespace App\Controller;

use App\Repository\CreditRepository;
use App\Repository\CarpoolingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    #[isGranted('ROLE_ADMIN')]
    public function index(CarpoolingRepository $carpoolingRepository): Response
    {
        $stats = $carpoolingRepository->countCarpoolingsByDay();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'carpooling_stats' => $stats,
        ]);
    }

    #[Route('/admin/stats/carpooling', name: 'admin_stats_carpooling')]
    #[IsGranted('ROLE_ADMIN')]
    public function carpoolingStats(CarpoolingRepository $carpoolingRepository): Response
    {
        $stats = $carpoolingRepository->countCarpoolingsByDay();

        return $this->render('admin/stats/carpooling.html.twig', [
            'carpooling_stats' => $stats,
        ]);
    }

    #[Route('/admin/stats/credits', name: 'admin_stats_credits')]
    #[IsGranted('ROLE_ADMIN')]
    public function creditStats(CreditRepository $creditRepository): Response
    {
        $stats = $creditRepository->sumCreditsByDay();
        $total = $creditRepository->getTotalCredits();

        return $this->render('admin/stats/credits.html.twig', [
            'credit_stats' => $stats,
            'total_credits' => $total,
        ]);
    }
}