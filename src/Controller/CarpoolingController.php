<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Credit;
use App\Entity\Carpooling;
use App\Form\CarpoolingType;
use App\Service\RatingService;
use App\Form\ReportProblemType;
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
    // Route for the index page : search Form and filter Form
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
        // Check if the user is connected
        $this->denyAccessUnlessGranted('ROLE_USER');

        // 1. Recover and verify the role
        /** @var User $user */
        $user = $this->getUser();
        // Nouveau test compatible avec tableau de rôles
        $roleTypes = $user->getRoleType(); // Ex: ['chauffeur'] ou ['passager']
        
        if (!array_intersect($roleTypes, ['chauffeur', 'chauffeur_passager'])) {
            $this->addFlash('danger', 'Seuls les chauffeurs peuvent proposer un trajet.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        // 2. Initialise the entity and link the user
        $carpooling = new Carpooling();
        $carpooling->setUsers($this->getUser());
        
        // 3. create the form via option 'user'
        $form = $this->createForm(CarpoolingType::class, $carpooling, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        // 4. Treatment of the form
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($carpooling);
            $entityManager->flush();

            $this->addFlash('success', 'Votre trajet a été enregistré ! 🚗');
            return $this->redirectToRoute('app_carpooling_index', [], Response::HTTP_SEE_OTHER);
        }

        // 5. display the form
        return $this->render('carpooling/new.html.twig', [
            'carpooling' => $carpooling,
            'form' => $form->createView(),
        ]);
    }

    // Route for the show page: display the details of a carpooling
    #[Route('/{id}', name: 'app_carpooling_show', methods: ['GET'], requirements: ['id' => '\d+'])]
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

    // Route for the carpoolingparticipation page
    #[Route('/{id}/participate', name: 'app_carpooling_participate', methods: ['GET'])]
    public function showParticipationConfirmation(Carpooling $carpooling): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // verify if the user is already connected
        if (!$this->getUser()) {
            $this->addFlash('Attention', 'Vous devez être connecté pour participer à un covoiturage !');
            return $this->redirectToRoute('app_login');
        }

        // Verify if the user is not already a passenger
        if ($carpooling->getPassengers()->contains($user)) {
            $this->addFlash('Attention', 'Vous participez déjà à ce covoiturage.');
            return $this->redirectToRoute('app_carpooling_show', ['id' => $carpooling->getId()]);
        }

        // Verify remaining seats
        if ($carpooling->getNumberSeats() <= 0) {
            $this->addFlash('Attention', 'Il n\'y a plus de place disponible pour ce trajet.');
            return $this->redirectToRoute('app_carpooling_show', ['id' => $carpooling->getId()]);
        }
        
         // Verify credits available
        $totalCredits = array_sum(array_map(
            fn($credit) => $credit->getBalance(),
            $user->getCredits()->toArray()
        ));

        $requiredCredits = 2;

        if ($totalCredits < $requiredCredits) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de crédits pour participer à ce covoiturage.');
            return $this->redirectToRoute('app_carpooling_show', ['id' => $carpooling->getId()]);
        }
        // --- DOUBLE CONFIRMATION (via GET/POST split) ---
            return $this->render('carpooling/confirm_participation.html.twig', [
                'carpooling' => $carpooling,
                'requiredCredits' => $requiredCredits,
                'totalCredits' => $totalCredits,
            ]);
    }

    // Route for the carpoolingparticipation confirmation page
    #[Route('/{id}/participate/confirm', name: 'app_carpooling_participate_confirm', methods: ['POST'])]
    public function confirmParticipation(Request $request, Carpooling $carpooling, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        //If User not connected
        if (!$user) {
            $this->addFlash('Attention', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // check the origin of the request [prevent a user from accessing the POST page directly without going through the comfirmation page]
        if (!$request->headers->has('referer') || !str_contains($request->headers->get('referer'), '/participate')) {
            $this->addFlash('warning', 'Merci de confirmer votre participation depuis la page prévue.');
            return $this->redirectToRoute('app_carpooling_participate', ['id' => $carpooling->getId()]);
        }

        // Verify token CSRF
        if (!$this->isCsrfTokenValid('participate'.$carpooling->getId(), $request->request->get('_token'))) {
            $this->addFlash('Attention', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_carpooling_participate', ['id' => $carpooling->getId()]);
        }

        // Double vérification côté serveur
        if ($carpooling->getPassengers()->contains($user) || $carpooling->getNumberSeats() <= 0) {
            $this->addFlash('Attention', 'Action non autorisée.');
            return $this->redirectToRoute('app_carpooling_show', ['id' => $carpooling->getId()]);
        }

        $totalCredits = array_sum(array_map(
            fn($credit) => $credit->getBalance(),
            $user->getCredits()->toArray()
        ));

        $requiredCredits = 10;

        if ($totalCredits < $requiredCredits) {
            $this->addFlash('danger', 'Crédits insuffisants.');
            return $this->redirectToRoute('app_carpooling_show', ['id' => $carpooling->getId()]);
        }

        // Save in the database
        // 1. deduct a credit
        $credit = new Credit();
        $credit->setBalance(-$requiredCredits);
        $credit->setTransactionDate(new \DateTime());
        $credit->setUsers($user);
        $entityManager->persist($credit);

        // 2. Add the user as a passenger
        $carpooling->addPassenger($user);

        // 3. Decrement the seats
        $carpooling->setNumberSeats($carpooling->getNumberSeats() - 1);

        $entityManager->persist($carpooling);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez rejoint ce covoiturage avec succès 🚗🎉');

        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/start/{id}', name: 'carpooling_start')]
    public function start(Carpooling $carpooling, EntityManagerInterface $em): Response
    {
        // verify if the user is the driver
        if ($carpooling->getUsers() !== $this->getUser()) {
            $this->addFlash('danger', 'Tu ne peux démarrer que tes propres trajets.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $carpooling->setStatus('en cours');
        $em->flush();

        $this->addFlash('success', 'Le trajet a démarré.');
        return $this->redirectToRoute('app_user_dashboard'); 
    }

    #[Route('/finish/{id}', name: 'carpooling_finish')]
    public function finish(Carpooling $carpooling, EntityManagerInterface $em): Response
    {
        // verify if the user is the driver
        if ($carpooling->getUsers() !== $this->getUser()) {
        $this->addFlash('danger', 'Tu ne peux terminer que tes propres trajets.');
        return $this->redirectToRoute('app_user_dashboard');
        }

        $carpooling->setStatus('terminé');
        $em->flush();

        $this->addFlash('success', 'Le trajet est terminé.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    // Road after the trip, only available after the trip is finished
    #[Route('/confirm/{id}', name: 'carpooling_confirm')]
    public function confirmTrip(Carpooling $carpooling): Response
    {

        $user = $this->getUser();

        // Vérifie que le trajet est terminé
        if ($carpooling->getStatus() !== 'terminé') {
            $this->addFlash('warning', 'Ce trajet n\'est pas encore terminé.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        // Vérifie que l'utilisateur est passager de ce trajet
        if (!$carpooling->getPassengers()->contains($user)) {
            $this->addFlash('danger', 'Tu ne participes pas à ce covoiturage.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('carpooling/confirm.html.twig', [
            'carpooling' => $carpooling,
        ]);
    }

        // Route called by the page "confirm" to say that the trip is ok
        #[Route('/confirm/{id}/success', name: 'carpooling_confirm_success', methods: ['POST'])]
    public function confirmTripSuccess(Carpooling $carpooling, EntityManagerInterface $em): Response 
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // verify that the user is connected
        if (!$user instanceof \App\Entity\User) {
            $this->addFlash('danger', 'Tu dois être connecté pour valider ce trajet.');
            return $this->redirectToRoute('app_login');
        }

        // verify that the carpooling is finished
        if ($carpooling->getStatus() !== 'terminé') {
        $this->addFlash('warning', 'Ce trajet n’est pas encore terminé.');
        return $this->redirectToRoute('app_user_dashboard');
        }

        // verify that the user is a passenger
        if (!$carpooling->getPassengers()->contains($user)) {
        $this->addFlash('danger', 'Tu ne peux pas valider ce trajet.');
        return $this->redirectToRoute('app_user_dashboard');
        }

        // add the user Id to the list of validations
        /* dump($user); dd(get_class($user)); */
        $carpooling->addValidatedPassengerId($user->getId());

        // Verify if all passengers have validated
        $allValidated = true;
        foreach ($carpooling->getPassengers() as $passenger) {
            if ($passenger instanceof \App\Entity\User) {
                if (!in_array($passenger->getId(), $carpooling->getValidatedPassengerIds())) {
                    $allValidated = false;
                    break;
                }
            } else {
                $allValidated = false; // sécurité si l'objet est corrompu
                break;
            }
        }
        // If all passengers have validated, debit the driver
        if ($allValidated) {
            // verify if there is a problem report
            if (!empty($carpooling->getProblemReports())) {
                $this->addFlash('warning', 'Un passager a signalé un problème. Le débit du chauffeur est suspendu jusqu’à traitement.');
                return $this->redirectToRoute('app_user_dashboard');
            }
            // debit the driver
            $credit = new Credit();
            $credit->setUsers($carpooling->getUsers()); // chauffeur
            $credit->setBalance(-2); // débit
            $credit->setTransactionDate(new \DateTime());

            $em->persist($credit);

            $this->addFlash('success', '✅ Tous les passagers ont validé. Le chauffeur est débité de 2 crédits.');
        }

        $em->flush();
        // add a flash message to inform the user and redirect to the dashboard
        $this->addFlash('success', 'Merci pour ta confirmation !');
        return $this->redirectToRoute('app_user_dashboard');
    }

        // route called ba the page "report_problem" to report a problem after the trip
        #[Route('/confirm/{id}/report', name: 'carpooling_report_problem')]
    public function reportProblem(Request $request, Carpooling $carpooling, EntityManagerInterface $em): Response 
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // verify that the user is connected
        if (!$user instanceof \App\Entity\User) {
            $this->addFlash('danger', 'Tu dois être connecté pour signaler un problème.');
            return $this->redirectToRoute('app_login');
        }

        // verify that the user is a passenger of this carpooling
        if (!$carpooling->getPassengers()->contains($user)) {
            $this->addFlash('danger', 'Tu ne peux signaler un problème que pour un trajet auquel tu as participé.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        // create the form to report a problem
        $form = $this->createForm(ReportProblemType::class);
        $form->handleRequest($request);

        // if the form is submitted and valid, add the report to the carpooling
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); //recover the content of the message field
            // save the report in the json field "problem_reports"
            $carpooling->addProblemReport($user->getId(), $data['message']);
            // save in the database
            $em->flush();
            // add a flash message to inform the user and redirect to the dashboard
            $this->addFlash('success', 'Merci pour ton retour. Un membre de l’équipe examinera ton signalement.');
            return $this->redirectToRoute('app_user_dashboard');
        }
        //display the form
        return $this->render('carpooling/report_problem.html.twig', [
            'carpooling' => $carpooling,
            'form' => $form->createView(),
        ]);
    }
}