<?php

namespace App\Controller;

use App\Service\RatingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RatingController extends AbstractController
{
    private RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    #[Route('/test-mongo', name: 'app_test_mongo')]
    public function testMongo(RatingService $ratingService): Response
    {
        $ratings = $ratingService->getRatingsForDriver(7);

        if (empty($ratings)) {
            return new Response('Aucune note trouvée pour le driver 7');
        }

        return new Response('<pre>' . print_r($ratings, true) . '</pre>');
    }

    
    #[Route('/driver/{id}/ratings', name: 'app_driver_ratings')]
    public function showRatings(int $id): Response
    {
        // Récupérer la moyenne des notes
        $averageRating = $this->ratingService->getAverageRatingForDriver($id);

        // Récupérer les commentaires
        $comments = $this->ratingService->getCommentsForDriver($id);

        return $this->render('rating/show.html.twig', [
            'driverId' => $id,
            'averageRating' => $averageRating,
            'comments' => $comments
        ]);
    }
}