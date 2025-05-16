<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (in_array('ROLE_EMPLOYEE_SUSPENDED', $user->getRoles())) {
            throw new CustomUserMessageAccountStatusException('Ce compte est suspendu.');
        }

        // Tu peux ajouter d'autres rôles si tu veux suspendre aussi des utilisateurs classiques
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // rien ici
    }
}