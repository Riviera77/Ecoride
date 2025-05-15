<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Credit;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();

        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Define Role by default
            $user->setRoles(['ROLE_USER']);

            // create first credit
            $credit = new Credit();
            $credit->setBalance(20);
            $credit->setTransactionDate(new \DateTime());
            $credit->setUsers($user); //link credit to user

            // Add to the collection (optionnel si relation bien configurée)
            $user->addCredit($credit);

            // Save the user and credit to the database
            $entityManager->persist($user);
            $entityManager->persist($credit); // important to persist the crédit
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec 20 crédits offerts 🎁');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}