<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\User;
use App\Entity\Credit;
use App\Entity\Carpooling;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        // --- Create a user ---
        $user1 = new User();
        $user1->setEmail("vanessa13@example.com");
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'vanessa13'));
        $user1->setRoles(["ROLE_USER"]);
        $user1->setUsername("Vanessa13");
        $user1->setPhoto('user1.png');
        $user1->setRoleType(['chauffeur_passager']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail("carla17@example.com");
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'carla178'));    
        $user2->setRoles(["ROLE_USER"]);
        $user2->setUsername("Carla17");
        $user2->setPhoto('user2.png');
        $user2->setRoleType(['chauffeur_passager']);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail("andre4125@example.com");
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'andre4125'));
        $user3->setRoles(["ROLE_USER"]);
        $user3->setUsername("Andre4125");
        $user3->setPhoto('user3.png');
        $user3->setRoleType(['chauffeur_passager']);
        $manager->persist($user3);
        
        $admin = new User();
        $admin->setEmail('admin@ecoride.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin321'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setUsername('Admin');
        $manager->persist($admin);

        $employee = new User();
        $employee->setEmail('employee@ecoride.com');
        $employee->setPassword($this->passwordHasher->hashPassword($employee, 'employee321'));
        $employee->setRoles(['ROLE_EMPLOYEE']);
        $employee->setUsername('Employé 1');
        $manager->persist($employee);

        // --- Create a car ---
        $car1 = new Car();
        $car1->setUsers($user1);
        $car1->setRegistration('1234ABCD');
        $car1->setDateFirstRegistration('2000-01-01');
        $car1->setModel('Clio');
        $car1->setColor('Bleu');
        $car1->setMark('Renault');
        $car1->setEnergy(false);
        $manager->persist($car1);

        $car2 = new Car();
        $car2->setUsers($user2);
        $car2->setRegistration('F58746CD');
        $car2->setDateFirstRegistration('2012-07-15');
        $car2->setModel('PROACE');
        $car2->setColor('noir');
        $car2->setMark('Toyota');
        $car2->setEnergy(true);
        $manager->persist($car2);

        $car3 = new Car();
        $car3->setUsers($user3);
        $car3->setRegistration('D956GR56');
        $car3->setDateFirstRegistration('2022-01-01');
        $car3->setModel('E-208');
        $car3->setColor('gris');
        $car3->setMark('peugeot');
        $car3->setEnergy(true);
        $manager->persist($car3);

        // --- Create carpoolings ---
        $carpooling1 = new Carpooling();
        $carpooling1->setUsers($user1);  // driver
        $carpooling1->setCars($car1); // car
        $carpooling1->setDepartureAddress('Paris');
        $carpooling1->setArrivalAddress('Lyon');
        $carpooling1->setDepartureDate(new \DateTime('2025-12-01'));
        $carpooling1->setArrivalDate(new \DateTime('2025-12-01'));
        $carpooling1->setDepartureTime(new \DateTime('08:00:00'));
        $carpooling1->setArrivalTime(new \DateTime('13:00:00'));
        $carpooling1->setPrice(50);
        $carpooling1->setNumberSeats(3);
        $carpooling1->setStatus('ouvert');
        $carpooling1->setPreference('non fumeur');
        // Add passengers
        $carpooling1->addPassenger($user2);  // user2 is passenger
        $user2->addCarpoolingsAsPassenger($carpooling1);
        $manager->persist($carpooling1);

        $carpooling2 = new Carpooling();
        $carpooling2->setUsers($user2);  // driver
        $carpooling2->setCars($car2); // car
        $carpooling2->setDepartureAddress('Paris');
        $carpooling2->setArrivalAddress('Rennes');
        $carpooling2->setDepartureDate(new \DateTime('2025-12-01'));
        $carpooling2->setArrivalDate(new \DateTime('2025-12-01'));
        $carpooling2->setDepartureTime(new \DateTime('08:30:00'));
        $carpooling2->setArrivalTime(new \DateTime('12:00:00'));
        $carpooling2->setPrice(30);
        $carpooling2->setNumberSeats(2);
        $carpooling2->setStatus('fermé');
        $carpooling2->setPreference('animaux acceptés');
        // Add passengers
        $carpooling2->addPassenger($user3);  // user3 is passenger
        $user3->addCarpoolingsAsPassenger($carpooling2);
        $carpooling2->addPassenger($user1);  // user1 is passenger
        $user1->addCarpoolingsAsPassenger($carpooling2);
        $manager->persist($carpooling2);

        $carpooling3 = new Carpooling();
        $carpooling3->setUsers($user3);  // driver
        $carpooling3->setCars($car3); // car
        $carpooling3->setDepartureAddress('Paris');
        $carpooling3->setArrivalAddress('Marseille');
        $carpooling3->setDepartureDate(new \DateTime('2026-01-01'));
        $carpooling3->setArrivalDate(new \DateTime('2026-01-01'));
        $carpooling3->setDepartureTime(new \DateTime('08:30:00'));
        $carpooling3->setArrivalTime(new \DateTime('15:30:00'));
        $carpooling3->setPrice(80);
        $carpooling3->setNumberSeats(2);
        $carpooling3->setStatus('ouvert');
        $carpooling3->setPreference('non fumeurs, animaux acceptés');
        // Add passengers
        $carpooling3->addPassenger($user1);  // user1 is passenger
        $user1->addCarpoolingsAsPassenger($carpooling3);
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

        $credit3 = new Credit($user3);
        $credit3->setBalance(10);
        $credit3->setTransactionDate(new \DateTime('2025-11-01'));
        $manager->persist($credit3);


        // --- Save in the database ---
        $manager->flush();
    }
}