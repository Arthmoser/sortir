<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\StatusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private CampusRepository $campusRepository;
    private StatusRepository $statusRepository;

    private Generator $faker;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher, CampusRepository $campusRepository, StatusRepository $statusRepository)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
        $this->campusRepository = $campusRepository;
        $this->statusRepository = $statusRepository;

    }


    public function load(ObjectManager $manager): void
    {
        $this->addUsers();
    }

    public function addUsers(){


        for($i=0; $i < 10; $i++){

            $campus = $this->campusRepository->find($this->faker->numberBetween(1,4));

            $user = new User();

            $user
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordHasher->hashPassword($user, $this->faker->password))
                ->setNickname($this->faker->userName)
                ->setLastname($this->faker->lastName)
                ->setFirstname($this->faker->firstName)
                ->setPhone($this->faker->phoneNumber)
                ->setIsAllowed($this->faker->boolean(90))
                ->setCampus($campus);

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }

    public function addActivities(){

        for($i=0; $i < 20; $i++){

            $status = $this->statusRepository->find($this->faker->numberBetween(1,4));
            $activity = new Activity();
            $campus = $this->campusRepository->find($this->faker->numberBetween(1,4));

            $activity
                ->setName($this->faker->name)
                ->setStartingDateTime($this->faker->dateTime)
                ->setDuration($this->faker->numberBetween(5, 120))
                ->setRegistrationDeadLine($this->faker->dateTime)
                ->setMaxRegistrationNb(50)
                ->setOverview($this->faker->sentences)
                ->setStatus($status)
                ->setLocation()
                ->setCampus($campus)
                ->setUser($this->faker->userName);

            $this->entityManager->persist($activity);
        }
        $this->entityManager->flush();
    }
}
