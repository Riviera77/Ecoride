<?php

namespace App\Controller;

use App\Entity\Carpooling;
use App\Form\CarpoolingType;
use App\Form\CarpoolingSearchType;
use App\Repository\CarpoolingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/carpooling')]
final class CarpoolingController extends AbstractController
{
    #[Route('/', name: 'app_carpooling_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CarpoolingRepository $carpoolingRepository): Response
    {
        $form = $this->createForm(CarpoolingSearchType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $carpoolings = [];
        $suggestions = [];

        if ($form->isSubmitted() && $form->isValid() && !empty(array_filter($form->getData()))) {
            $criteria = $form->getData();
            $carpoolings = $carpoolingRepository->findBySearchCriteria($criteria);
            
            if (empty($carpoolings)) {
                // Suggestion alternative : trajets le même jour sans le prix
                $suggestions = $carpoolingRepository->findSimilarRides($criteria);
            }            
        }
        else {
            // fallback : affiche tous les trajets avec places restantes sans critères de recherche
            /* $carpoolings = $carpoolingRepository->createQueryBuilder('c')
            ->where('c.numberSeats > 0')
            ->getQuery()
            ->getResult(); */
            $carpoolings = []; // Ne rien afficher par défaut
            $suggestions = [];
        }
        /* // filter by search criteria otherwise show everything
        if (!empty($criteria)) {
            $carpoolings = $carpoolingRepository->findBy($criteria);
        } else {
            $carpoolings = $carpoolingRepository->findAll();
        } */

        return $this->render('carpooling/index.html.twig', [
            'form' => $form->createView(),
            'carpoolings' => $carpoolings,
            'suggestions' => $suggestions,
        ]);
    }

    #[Route('/new', name: 'app_carpooling_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carpooling = new Carpooling();
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
    public function show(Carpooling $carpooling): Response
    {
        return $this->render('carpooling/show.html.twig', [
            'carpooling' => $carpooling,
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