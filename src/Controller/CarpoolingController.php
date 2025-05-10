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
        $form = $this->createForm(CarpoolingSearchType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        // Always recover the search parameters from the query string GET:
        /* $searchParams = $request->query->get('carpooling_search') ?? []; // return array with key or array empty */
        $carpoolings = [];
        $suggestions = [];

        //1- First search for carpoolings if the form is submitted 
        if ($form->isSubmitted() && $form->isValid() && !empty(array_filter($form->getData()))) {
            $criteria = $form->getData();
            $carpoolings = $carpoolingRepository->findBySearchCriteria($criteria);
            /* $searchParams = $criteria; // save criteria for the next filter form */

            if (empty($carpoolings)) {
                // alternative Suggestion  : carpooling the same day without the price
                $suggestions = $carpoolingRepository->findSimilarRides($criteria);
            }            
        }

        /* dd('Request GET', $request->query->all());
        dd('Carpoolings (avant le if)', $carpoolings); */

        // 2- if filter form is submitted without carpoolings found, relaunch the first search
        /* if ($request->query->has('carpooling_filter') && empty($carpoolings) && !empty($searchParams)) {
            $carpoolings = $carpoolingRepository->findBySearchCriteria($searchParams);
            dd('Carpoolings rechargés depuis search (car filtre soumis)', $carpoolings);
        } */
        
        // 2- prepare form filter if filter form is submitted with carpoolings found
        $filterForm = null;

        if (!empty($carpoolings)) {
            $filterForm = $this->createForm(CarpoolingFilterType::class, null, [
                'method' => 'GET'
            ]);

            $filterForm->handleRequest($request);
            dump($request->query->all());
            /* dd('filterForm soumis ?', $filterForm->isSubmitted()); */

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                $filterData = $filterForm->getData();
                dump('filterData', $filterData);

                // recover data from the first form (GET)
                $searchData = $request->query->all('carpooling_search') ?? [];

                // merge data from the first form and the filter form
                $mergedCriteria = array_merge($searchData, $filterData);
                dump('Merged criteria', $mergedCriteria);

                // unique launch with the first search with the merged criteria
                $carpoolings = $carpoolingRepository->findBySearchAndFilterCriteria($mergedCriteria, $ratingService);
                
            }
        }

        // 3- build the query string to keep criteria search
                /* if (!is_array($searchParams)) {
                    $searchParams = []; // Sécurité : éviter une erreur si c'est null
                }
                $queryString = http_build_query(['carpooling_search' => $searchParams]);
                dump('Query string envoyée au Twig :', $queryString);
                */
                /* $searchParams = $request->query->get('carpooling_search') ?? [];
                $queryString = http_build_query(['carpooling_search' => $searchParams]); */
                /* $filterFormAction = $this->generateUrl('app_carpooling_index', ['carpooling_search' => $searchData], UrlGeneratorInterface::ABSOLUTE_PATH); */
                $searchData = $request->query->all('carpooling_search') ?? [];
                $queryString = http_build_query($request->query->all());
                $filterFormAction = $this->generateUrl('app_carpooling_index') . '?' . $queryString;

        return $this->render('carpooling/index.html.twig', [
            'form' => $form->createView(),
            'filterForm' => $filterForm ? $filterForm->createView() : null,
            'carpoolings' => $carpoolings,
            'suggestions' => $suggestions,
            'filterFormAction' => $filterFormAction,
            /* 'queryString' => $queryString, */
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