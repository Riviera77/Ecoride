<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\User;
use App\Entity\Credit;
use App\Entity\Carpooling;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // --- Create a user ---
        $user1 = new User();
        $user1->setEmail("vanessa13@example.com");
        $user1->setPassword("vanessa13"); // password not hashed (juste for the tests)
        $user1->setRoles(["ROLE_USER"]);
        $user1->setUsername("Vanessa13");
        $user1->setPhoto('public/uploads/user1.png');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail("carla17@example.com");
        $user2->setPassword("carla17"); // password not hashed (juste for the tests)    
        $user2->setRoles(["ROLE_USER"]);
        $user2->setUsername("Carla17");
        $user2->setPhoto('public/uploads/user2.png');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail("andre4125@example.com");
        $user3->setPassword("andre4125"); // password not hashed (juste for the tests)  
        $user3->setRoles(["ROLE_USER"]);
        $user3->setUsername("Andre4125");
        $user3->setPhoto('public/uploads/user3.png');
        $manager->persist($user3);
        
        // --- Create a car ---
        $car1 = new Car($user1);
        $car1->setRegistration('1234ABCD');
        $car1->setDateFirstRegistration('2000-01-01');
        $car1->setModel('Clio');
        $car1->setColor('Bleu');
        $car1->setMark('Renault');
        $car1->setEnergy(false);
        $manager->persist($car1);

        $car2 = new Car($user2);
        $car2->setRegistration('F58746CD');
        $car2->setDateFirstRegistration('2012-07-15');
        $car2->setModel('PROACE');
        $car2->setColor('noir');
        $car2->setMark('Toyota');
        $car2->setEnergy(false);
        $manager->persist($car2);

        $car3 = new Car($user3);
        $car3->setRegistration('D956GR56');
        $car3->setDateFirstRegistration('2022-01-01');
        $car3->setModel('E-208');
        $car3->setColor('gris');
        $car3->setMark('peugeot');
        $car3->setEnergy(true);
        $manager->persist($car3);

        // --- Create a carpooling ---
        $carpooling1 = new Carpooling($user1, $car1);
        $carpooling1->setDepartureAddress('Paris');
        $carpooling1->setArrivalAddress('Lyon');
        $carpooling1->setDepartureDate(new \DateTime('2025-12-01'));
        $carpooling1->setArrivalDate(new \DateTime('2025-12-01'));
        $carpooling1->setDepartureTime(new \DateTime('08:00:00'));
        $carpooling1->setArrivalTime(new \DateTime('13:00:00'));
        $carpooling1->setPrice(50);
        $carpooling1->setNumberSeats(3);
        $carpooling1->setStatus('open');
        $carpooling1->setPreference('non fumeur');
        $manager->persist($carpooling1);

        $carpooling2 = new Carpooling($user2, $car2);
        $carpooling2->setDepartureAddress('Paris');
        $carpooling2->setArrivalAddress('Rennes');
        $carpooling2->setDepartureDate(new \DateTime('2025-12-01'));
        $carpooling2->setArrivalDate(new \DateTime('2025-12-01'));
        $carpooling2->setDepartureTime(new \DateTime('08:30:00'));
        $carpooling2->setArrivalTime(new \DateTime('12:00:00'));
        $carpooling2->setPrice(30);
        $carpooling2->setNumberSeats(2);
        $carpooling2->setStatus('open');
        $carpooling2->setPreference('animaux acceptés');
        $manager->persist($carpooling2);

        $carpooling3 = new Carpooling($user3, $car3);
        $carpooling3->setDepartureAddress('Paris');
        $carpooling3->setArrivalAddress('Rennes');
        $carpooling3->setDepartureDate(new \DateTime('2025-12-01'));
        $carpooling3->setArrivalDate(new \DateTime('2025-12-01'));
        $carpooling3->setDepartureTime(new \DateTime('07:30:00'));
        $carpooling3->setArrivalTime(new \DateTime('15:20:00'));
        $carpooling3->setPrice(80);
        $carpooling3->setNumberSeats(2);
        $carpooling3->setStatus('open');
        $carpooling3->setPreference('non fumeurs, animaux acceptés');
        $manager->persist($carpooling3);

        // --- Create credits ---
        $credit1 = new Credit($user1);
        $credit1->setBalance(20);
        $credit1->setTransactionDate(new \DateTime('2025-11-01'));
        $manager->persist($credit1);

        $credit2 = new Credit($user2);
        $credit2->setBalance(16);
        $credit2->setTransactionDate(new \DateTime('2025-11-01'));
        $manager->persist($credit2);

        // --- Save in the database ---
        $manager->flush();
    }
}