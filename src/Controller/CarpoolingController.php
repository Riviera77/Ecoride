<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Form\CarpoolingType;
use App\Service\RatingService;
use App\Form\CarpoolingFilterType;
use App\Form\CarpoolingSearchType;
use App\Repository\CarpoolingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/carpooling')]
final class CarpoolingController extends AbstractController
{
    #[Route('/', name: 'app_carpooling_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CarpoolingRepository $carpoolingRepository, RatingService $ratingService): Response
    {
        $carpoolings = [];
        $suggestions = [];

        // 1. Create and treate search form
        $searchForm = $this->createForm(CarpoolingSearchType::class, null, [
            'method' => 'GET',
        ]);
        $searchForm->handleRequest($request);

        // Data of search
        $searchData = $request->query->all('carpooling_search');

        // if research data is present (either via form or URL), the search is made 
        if (!empty(array_filter($searchData))) {
            $carpoolings = $carpoolingRepository->findBySearchCriteria($searchData);

            if (empty($carpoolings)) {
                $suggestions = $carpoolingRepository->findSimilarRides($searchData);
            }
        }

        // 2. Create and treatment filter form (always instantiated)
        $filterForm = $this->createForm(CarpoolingFilterType::class, null, [
            'method' => 'GET'
        ]);
        $filterForm->handleRequest($request);

        // Data of filter
        $filterData = $request->query->all('carpooling_filter');

        // if filter is submitted, relaunch the first search, then apply the filter
        if (!empty(array_filter($filterData))) {
            // if carpoolings not search yet, I recover it
            if (empty($carpoolings) && !empty($searchData)) {
                $carpoolings = $carpoolingRepository->findBySearchCriteria($searchData);
            }

            // then apply the filter
            $mergedCriteria = array_merge($searchData, $filterData);
            $carpoolings = $carpoolingRepository->findBySearchAndFilterCriteria($mergedCriteria, $ratingService);
        }

        // 3. Generation of the URL for the filter form (action with search settings)
        $queryString = http_build_query(['carpooling_search' => $searchData]);
        $filterFormAction = $this->generateUrl('app_carpooling_index') . '?' . $queryString;

        return $this->render('carpooling/index.html.twig', [
            'form' => $searchForm->createView(),
            'filterForm' => count($carpoolings) > 0 ? $filterForm->createView() : null,
            'carpoolings' => $carpoolings,
            'suggestions' => $suggestions,
            'filterFormAction' => $filterFormAction,
        ]);
    }

    #[Route('/new', name: 'app_carpooling_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carpooling = new Carpooling();
        $carpooling->setUsers($this->getUser());
        
        $form = $this->createForm(CarpoolingType::class, $carpooling);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($carpooling);
            $entityManager->flush();

            return $this->redirectToRoute('app_carpooling_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carpooling/new.html.twig', [
            'carpooling' => $carpooling,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carpooling_show', methods: ['GET'])]
    public function show(Carpooling $carpooling, RatingService $ratingService): Response
    {
        // recover object User($driverId) : the driver
        $driverId = $carpooling->getUsers();
        // recover the average rating for the driver
        $averageRating = $ratingService->getAverageRatingForDriver($driverId->getId());
        // recover the comments for the driver
        $comments = $ratingService->getCommentsForDriver($driverId->getId());

        return $this->render('carpooling/show.html.twig', [
            'carpooling' => $carpooling,
            'averageRating' => $averageRating,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_carpooling_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Carpooling $carpooling, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarpoolingType::class, $carpooling);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_carpooling_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carpooling/edit.html.twig', [
            'carpooling' => $carpooling,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carpooling_delete', methods: ['POST'])]
    public function delete(Request $request, Carpooling $carpooling, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carpooling->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($carpooling);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_carpooling_index', [], Response::HTTP_SEE_OTHER);
    }
}