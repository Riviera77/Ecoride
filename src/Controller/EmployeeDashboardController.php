<?php

namespace App\Controller;

use App\Service\RatingService;
use App\Service\IncidentService;
use App\Repository\UserRepository;
use App\Repository\CarpoolingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class EmployeeDashboardController extends AbstractController
{
    #[Route('/employee', name: 'employee_dashboard')]
    #[IsGranted('ROLE_EMPLOYEE')]   

    private RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    #[Route('/employee', name: 'employee_dashboard')]
    #[IsGranted('ROLE_EMPLOYEE')] 
    public function index(): Response
    {
        return $this->render('employee_dashboard/index.html.twig', [
            'controller_name' => 'EmployeeDashboardController',
        ]);
    }

    #[Route('/reviews', name: 'employee_review_list')]
    public function reviewList(RatingService $ratingService, UserRepository $userRepository, CarpoolingRepository $carpoolingRepository): Response
    {
        // recover all ratings with status 'pending' from MongoDB
        $pendingReviews = $ratingService->getPendingReviews();

        // prepare array for the vue
        $enrichedReviews = [];

        foreach ($pendingReviews as $review) {
            // recover all objects User from PostgreSQL for driver and author passengers
            $driver = $userRepository->find($review['driverId'] ?? 0);
            $author = $userRepository->find($review['authorId'] ?? 0);

            // recover the trip concerned
            $trip = $carpoolingRepository->find($review['carpoolingId'] ?? 0);

            $enrichedReviews[] = [
                'id' => (string) $review['_id'],
                'rating' => $review['rating'] ?? null,
                'comment' => $review['comment'] ?? '',
                'driver' => $driver,
                'author' => $author,
                'trip' => $trip,
            ];
        }

        return $this->render('employee/review_list.html.twig', [
            'reviews' => $enrichedReviews,
        ]);
    }

    #[Route('/review/{id}/approve', name: 'employee_review_approve')]
    public function approveReview(string $id): Response
    {
        $this->ratingService->approveReview($id);

        $this->addFlash('success', 'Avis approuvé avec succès ✅');
        return $this->redirectToRoute('employee_review_list');
    }

    #[Route('/review/{id}/reject', name: 'employee_review_reject')]
    public function rejectReview(string $id): Response
    {
        $this->ratingService->rejectReview($id);

        $this->addFlash('warning', 'Avis rejeté ❌');
        return $this->redirectToRoute('employee_review_list');
    }

    #[Route('/bad-trips', name: 'employee_bad_trips')]
    public function badTrips(IncidentService $incidentService, UserRepository $userRepository, CarpoolingRepository $carpoolingRepository): Response 
    {
        // 1. Récupère tous les incidents stockés dans MongoDB
        $incidents = $incidentService->getAllIncidents();

        $enrichedIncidents = [];

        // 2. Pour chaque incident, enrichit les données avec le trajet et le passager
        foreach ($incidents as $incident) {
            // a. Retrouve le trajet depuis PostgreSQL
            $trip = $carpoolingRepository->find((int) $incident['carpoolingId']);
            // b. Retrouve le passager depuis PostgreSQL
            $reporter = $userRepository->find((int) $incident['reporterId']);

            // c. Construit un tableau enrichi pour la vue Twig
            $enrichedIncidents[] = [
                'trip' => $trip,
                'reporter' => $reporter,
                'message' => $incident['message'],
                'createdAt' => $incident['createdAt']->toDateTime()->format('d/m/Y'),
            ];
        }

        return $this->render('employee/bad_trips.html.twig', [
            'incidents' => $enrichedIncidents,
        ]);
    }
}