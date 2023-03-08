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
        $number = 50;

        $this->addCampuses();
        $this->addStatuses();
        $this->addUsers();
        $this->addCities();
        $this->addLocations();
        $this->addActivities();
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

    public function addUsers(){

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

        for($i=0; $i < $this->number; $i++){

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


    public function addCities(){

        for($i=0; $i < $this->number; $i++){

            $city = new City();

            $city
                ->setName($this->faker->city)
                ->setZipCode($this->faker->postcode);

            $this->entityManager->persist($city);
        }
        $this->entityManager->flush();
    }

    public function addLocations(){

        for($i=0; $i < $this->number; $i++){

            $city = $this->cityRepository->find($this->faker->numberBetween(1,$this->number));

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

    public function addActivities(){

        $activities = ['Faire du ménage', 'Faire du sport', 'Escape Game outdoor Harry Potter ', 'Jeu de Piste avec un Smartphone',
            'E-hockey© en Hoverboard', 'Découverte des secrets de l\'horlogerie', 'Balade en 2CV', 'Atelier création de parfum',
            'Atelier café aux méthodes douces', 'Atelier cocktails modernes', 'Visite guidée à vélo', ' Visite guidée: romantisme au cinéma',
            'Quiz Room', 'Atelier apéro-peinture dans le noir', 'Tricoter des C#', 'Patinoire', 'Bowling', 'Billard', 'Karaoké', 'Aquarium',
            'Journée shopping', 'Borne d’arcade', 'Géocaching ou jeu de piste', 'Escape game', 'Dégustation de vin dans une cave locale ou de bière chez un brasseur du coin',
            'Festival de musique', 'Tester un nouveau restaurant', 'Club de jazz', 'Jouer au Spikeball', 'Randonnée', 'Prendre un cours ensemble : danse, poterie, cuisine,...',
            'Promenade à cheval', 'Jouer à La balle aux prisonniers', 'Pique-nique dans un parc ou sur la plage', 'Organiser un match de football, baseball, volley...',
            'Cueillette de fruits et légumes dans une ferme ou un verger local', 'Jouer au frisbee', 'Camping...dans un vrai camping ou dans le jardin !', 'Promenade en vélo',
            'Jouer au Badminton dans un parc', 'Laser game', 'Paint ball', 'Concert ou pièce de théâtre', 'Ramasser les déchets dans un parc ou une foret',
            'Partez en road trip pour la journée', 'Parc d’attraction, fête foraine ou parc aquatique', 'Pédicure ou manucure', 'Promenade en bateau ou en barque',
            'Jouez aux fléchettes', 'Boire un verre dans un bar à chats'];


        for($i=0; $i < count($activities); $i++){

            $index = 0;

            $activity = new Activity();

            $statuses = $this->statusRepository->findAll();
            $campus = $this->campusRepository->find($this->faker->numberBetween(1,4));
            $location = $this->locationRepository->find($this->faker->numberBetween(1,$this->number));
            $user = $this->userRepository->find($this->faker->numberBetween(1,$this->number));

            $activity
                ->setName($activities[$i])
                ->setStartingDateTime($this->faker->dateTimeBetween('-2 month', '+3 month'))
                ->setDuration($this->faker->numberBetween(5, 120));

            $date = clone $activity->getStartingDateTime();

            $activity
                ->setRegistrationDeadLine($date->modify('-7 day'))
                ->setMaxRegistrationNb($this->faker->numberBetween(2, 25))
                ->setOverview(implode(" ", $this->faker->words(50)));

            if ($activity->getStartingDateTime() < date('Y=m=d H:i:s', strtotime('-1 month'))) {
                $index = 2;
            } elseif ($activity->getStartingDateTime() < date('Y=m=d H:i:s')) {
                $index = 4;
            } elseif ($activity->getStartingDateTime() == date('Y=m=d H:i:s')) {
                $index = 3;
            } else {
                $index = $this->faker->randomElement([0, 1, 5]);
            }

            $activity
                ->setStatus($statuses[$index])
                ->setLocation($location)
                ->setCampus($campus)
                ->setUser($user);

            $this->entityManager->persist($activity);
        }
        $this->entityManager->flush();
    }
}
