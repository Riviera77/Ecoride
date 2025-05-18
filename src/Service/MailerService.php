<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendTripFinishedEmail(string $to, string $username, int $carpoolingId): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@ecoride.com', 'EcoRide'))
            ->to($to)
            ->subject('🚗 Votre covoiturage est terminé')
            ->htmlTemplate('emails/trip_finished.html.twig')
            ->context([
                'username' => $username,
                'carpoolingId' => $carpoolingId,
            ]);

        $this->mailer->send($email);
    }
}