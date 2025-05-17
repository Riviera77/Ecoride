<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Credit;
use App\Form\User1Type;
use App\Entity\Carpooling;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\CarpoolingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/dashboard', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(CarpoolingRepository $carpoolingRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Recover carpoolings created by user (driver)
        $carpoolings = $carpoolingRepository->findBy(['users' => $user]);

        return $this->render('user/dashboard.html.twig', [
            'carpoolings' => $carpoolings,
        ]);
    }

    #[Route('/history', name: 'app_user_history')]
    public function history(CarpoolingRepository $carpoolingRepository, Security $security): Response 
    {
        $user = $security->getUser();
        $carpoolings = $carpoolingRepository->findAllByUserParticipation($user);

        return $this->render('user/history.html.twig', [
            'carpoolings' => $carpoolings,
            'currentUser' => $user,
        ]);
    }

    #[Route('/user/carpooling/{id}/cancel-driver', name: 'cancel_carpooling_driver', methods: ['POST'])]
    public function cancelAsDriver(Carpooling $carpooling, EntityManagerInterface $em, Security $security, MailerInterface $mailer): Response
    {
        $user = $security->getUser();

        if ($carpooling->getUsers() !== $user) {
            throw $this->createAccessDeniedException("Tu n'es pas le chauffeur de ce covoiturage.");
        }

        // Send email to passengers before canceling
        foreach ($carpooling->getPassengers() as $passenger) {
            $email = (new Email())
                ->from('noreply@ecoride.com')
                ->to($passenger->getEmail())
                ->subject('🛑 Annulation de votre covoiturage')
                ->html("
                    <p>Bonjour {$passenger->getUsername()},</p>
                    <p>Le covoiturage que vous aviez réservé a été <strong>annulé</strong> par le chauffeur.</p>
                    <ul>
                        <li><strong>Départ :</strong> {$carpooling->getDepartureAddress()}</li>
                        <li><strong>Arrivée :</strong> {$carpooling->getArrivalAddress()}</li>
                        <li><strong>Date :</strong> {$carpooling->getDepartureDate()->format('d/m/Y')}</li>
                        <li><strong>Heure :</strong> {$carpooling->getDepartureTime()->format('H:i')}</li>
                    </ul>
                    <p>Nous vous prions de nous excuser pour la gêne occasionnée.</p>
                    <p>— L’équipe Ecoride 🌱</p>
                ");
            $mailer->send($email);
        }

        // Remove all passengers from the carpooling
        foreach ($carpooling->getPassengers() as $passenger) {
            $carpooling->removePassenger($passenger);
        }

        // Repay/recover 2 credits for the driver
        $credit = new Credit();
        $credit->setUsers($user);
        $credit->setBalance(2);
        $credit->setTransactionDate(new \DateTimeImmutable());

        // cancel carpooling
        $em->remove($carpooling);
        $em->persist($credit);
        $em->flush();

        $this->addFlash('success', 'Covoiturage annulé. 2 crédits t’ont été rendus.');

        return $this->redirectToRoute('app_user_history');
    }

    #[Route('/user/carpooling/{id}/cancel-passenger', name: 'cancel_carpooling_passenger', methods: ['POST'])]
    public function cancelAsPassenger(Carpooling $carpooling, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$carpooling->getPassengers()->contains($user)) {
            throw $this->createAccessDeniedException("Tu ne participes pas à ce covoiturage.");
        }
        //cancel passenger
        $carpooling->removePassenger($user);

        // let a seat available
        $carpooling->setNumberSeats($carpooling->getNumberSeats() + 1);

        //recover 2 credits
        $credit = new Credit();
        $credit->setUsers($user);
        $credit->setBalance(2); 
        $credit->setTransactionDate(new \DateTimeImmutable());


        $em->persist($carpooling);
        $em->persist($credit);
        $em->flush();

        $this->addFlash('success', 'Tu as quitté le covoiturage.');

        return $this->redirectToRoute('app_user_history');
    }
}