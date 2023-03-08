<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\Status;
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

    private $number = 50;

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
        $this->addCampuses();
        $this->addStatuses();
        $this->addUsers($this->number);
        $this->addCities($this->number);
        $this->addLocations($this->number);
        $this->addActivities($this->number);
    }


    public function addCampuses(){

            $campusList = ['Nantes', 'Rennes', 'Quimper', 'Niort'];

            foreach ($campusList as  $campus){

            $currentCampus = new Campus();
            $currentCampus->setName($campus);

            $this->entityManager->persist($currentCampus);

            }

        $this->entityManager->flush();
    }


    public function addStatuses(){

        $statusList = ['Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'passée', 'Annulée'];

        foreach ($statusList as  $status){

            $currentStatus = new Status();
            $currentStatus->setType($status);

            $this->entityManager->persist($currentStatus);

        }

        $this->entityManager->flush();
    }

    public function addUsers($number){

        $userAdmin = new User();
        $userAdmin->setEmail('stropee@campus-eni.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($userAdmin, 'Pa$$w0rd'))
            ->setNickname('Sly')
            ->setLastname('Tropée')
            ->setFirstname('Sylvain')
            ->setPhone('0620304050')
            ->setIsAllowed(true)
            ->setCampus($this->campusRepository->find(2))
            ->setProfilePicture('profilePicture.png');

        $this->entityManager->persist($userAdmin);

        for($i=0; $i < $number; $i++){

            $campus = $this->campusRepository->find($this->faker->numberBetween(1,4));

            $user = new User();

            $user
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordHasher->hashPassword($user, 'Pa$$w0rd'))
                ->setNickname($this->faker->userName)
                ->setLastname($this->faker->lastName)
                ->setFirstname($this->faker->firstName)
                ->setPhone($this->faker->phoneNumber)
                ->setIsAllowed($this->faker->boolean(90))
                ->setCampus($campus)
                ->setProfilePicture('profilePicture.png');

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }


    public function addCities($number){

        for($i=0; $i < $number; $i++){

            $city = new City();

            $city
                ->setName($this->faker->city)
                ->setZipCode($this->faker->postcode);

            $this->entityManager->persist($city);
        }
        $this->entityManager->flush();
    }

    public function addLocations($number){

        for($i=0; $i < $number; $i++){

            $city = $this->cityRepository->find($this->faker->numberBetween(1,30));

            $location = new Location();

            $location
                ->setName($this->faker->name)
                ->setStreet($this->faker->streetAddress)
                ->setLatitude($this->faker->latitude)
                ->setLongitude($this->faker->longitude)
                ->setCity($city);

            $this->entityManager->persist($location);
        }
        $this->entityManager->flush();
    }

    public function addActivities($number){

        for($i=0; $i < $number; $i++){

            $activity = new Activity();

            $status = $this->statusRepository->find($this->faker->numberBetween(1,6));
            $campus = $this->campusRepository->find($this->faker->numberBetween(1,4));
            $location = $this->locationRepository->find($this->faker->numberBetween(1,$number));
            $user = $this->userRepository->find($this->faker->numberBetween(1,$number));

            $activity
                ->setName($this->faker->name)
                ->setStartingDateTime($this->faker->dateTimeBetween('-3 month', '+2 month'))
                ->setDuration($this->faker->numberBetween(5, 120));

            $date= clone $activity->getStartingDateTime();

            $activity
                ->setRegistrationDeadLine($date->modify('-7 day'))
                ->setMaxRegistrationNb($this->faker->numberBetween(2, 25))
                ->setOverview(implode(" ", $this->faker->words($number)))
                ->setStatus($status)
                ->setLocation($location)
                ->setCampus($campus)
                ->setUser($user);

            $this->entityManager->persist($activity);
        }
        $this->entityManager->flush();
    }
}
