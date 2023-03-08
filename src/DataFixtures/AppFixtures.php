<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
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
    private LocationRepository $locationRepository;
    private UserRepository $userRepository;
    private CityRepository $cityRepository;
    private Generator $faker;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher, CampusRepository $campusRepository,
        StatusRepository $statusRepository, LocationRepository $locationRepository,
        UserRepository $userRepository, CityRepository $cityRepository)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
        $this->campusRepository = $campusRepository;
        $this->statusRepository = $statusRepository;
        $this->locationRepository = $locationRepository;
        $this->userRepository = $userRepository;
        $this->cityRepository = $cityRepository;
    }


    public function load(ObjectManager $manager): void
    {
        $this->addUsers();
        $this->addLocations();
        $this->addActivities();
        $this->addCities();
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

    public function addLocations(){

        for($i=0; $i < 50; $i++){

            $location = new Location();
            $city = $this->cityRepository->find($this->faker->numberBetween(1,50));
            $location
                ->setName($this->faker->name)
                ->setCity($city)
                ->setLatitude($this->faker->latitude)
                ->setLongitude($this->faker->longitude)
                ->setStreet($this->faker->streetAddress);

            $this->entityManager->persist($location);
        }
        $this->entityManager->flush();
    }

    public function addCities(){

        for($i=0; $i < 50; $i++){

            $city = new City();

            $city
                ->setName($this->faker->name)
                ->setZipCode($this->faker->postcode);

            $this->entityManager->persist($city);
        }
        $this->entityManager->flush();
    }
    public function addActivities(){

        for($i=0; $i < 20; $i++){

            $activity = new Activity();

            $status = $this->statusRepository->find($this->faker->numberBetween(1,6));
            $campus = $this->campusRepository->find($this->faker->numberBetween(1,4));
            $location = $this->locationRepository->find($this->faker->numberBetween(1,4));
            $user = $this->userRepository->find($this->faker->numberBetween(1,4));

            $activity
                ->setName($this->faker->name)
                ->setStartingDateTime($this->faker->dateTime)
                ->setDuration($this->faker->numberBetween(5, 120))
                ->setRegistrationDeadLine($this->faker->dateTime)
                ->setMaxRegistrationNb(50)
                ->setOverview($this->faker->sentences)
                ->setStatus($status)
                ->setLocation($location)
                ->setCampus($campus)
                ->setUser($user);

            $this->entityManager->persist($activity);
        }
        $this->entityManager->flush();
    }
}
