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

    private $campuses;
    private $statuses;
    private $cities;
    private $locations;
    private $users;

    private $statusCreated = 'CRE';
    private $statusOpened = 'OUV';
    private $statusClosed = 'CLO';
    private $statusInProgress = 'AEC';
    private $statusFinished = 'PAS';
    private $statusCanceled = 'ANN';
    private $statusArchived = 'HIS';

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
        $this->addCities();
        $this->addLocations();
        $this->addUsers();
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

        $this->campuses = $this->campusRepository->findAll();

    }


    public function addStatuses(){

        $statusList = ['CRE' => 'Cr????e', 'OUV' => 'Ouverte', 'CLO' => 'Cl??tur??e', 'AEC' => 'Activit?? en cours', 'PAS' => 'Pass??e', 'ANN' => 'Annul??e', 'HIS' => 'Historis??e'];

        foreach ($statusList as $key => $status){

            $currentStatus = new Status();
            $currentStatus->setType($status);
            $currentStatus->setStatusCode($key);

            $this->entityManager->persist($currentStatus);

        }

        $this->entityManager->flush();

        $this->statuses = $this->statusRepository->findAll();
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

        $this->cities = $this->cityRepository->findAll();

    }


    public function addLocations(){

        for($i=0; $i < $this->number; $i++){


            $location = new Location();

            $location
                ->setName($this->faker->name)
                ->setStreet($this->faker->streetAddress)
                ->setLatitude($this->faker->latitude)
                ->setLongitude($this->faker->longitude)
                ->setCity($this->faker->randomElement($this->cities));

            $this->entityManager->persist($location);
        }
        $this->entityManager->flush();


        $this->locations = $this->locationRepository->findAll();

    }

    public function addUsers(){

        $userAdmin = new User();
        $userAdmin->setEmail('stropee@campus-eni.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($userAdmin, 'Pa$$w0rd'))
            ->setNickname('Sly')
            ->setLastname('Trop??e')
            ->setFirstname('Sylvain')
            ->setPhone('0620304050')
            ->setIsAllowed(true)
            ->setCampus($this->faker->randomElement($this->campuses))
            ->setProfilePicture('profilePicture.png');

        $this->entityManager->persist($userAdmin);



        for($i=0; $i < $this->number; $i++){


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
                ->setCampus($this->faker->randomElement($this->campuses))
                ->setProfilePicture('profilePicture.png');

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        $this->users = $this->userRepository->findAll();

    }


    public function addActivities(){

        $activities = ['Faire du m??nage', 'Faire du sport', 'Escape Game outdoor Harry Potter ', 'Jeu de Piste avec un Smartphone',
            'E-hockey?? en Hoverboard', 'D??couverte des secrets de l\'horlogerie', 'Balade en 2CV', 'Atelier cr??ation de parfum',
            'Atelier caf?? aux m??thodes douces', 'Atelier cocktails modernes', 'Visite guid??e ?? v??lo', ' Visite guid??e: romantisme au cin??ma',
            'Quiz Room', 'Atelier ap??ro-peinture dans le noir', 'Tricoter des C#', 'Patinoire', 'Bowling', 'Billard', 'Karaok??', 'Aquarium',
            'Journ??e shopping', 'Borne d???arcade', 'G??ocaching ou jeu de piste', 'Escape game', 'D??gustation de vin dans une cave locale ou de bi??re chez un brasseur du coin',
            'Festival de musique', 'Tester un nouveau restaurant', 'Club de jazz', 'Jouer au Spikeball', 'Randonn??e', 'Prendre un cours ensemble : danse, poterie, cuisine,...',
            'Promenade ?? cheval', 'Jouer ?? La balle aux prisonniers', 'Pique-nique dans un parc ou sur la plage', 'Organiser un match de football, baseball, volley...',
            'Cueillette de fruits et l??gumes dans une ferme ou un verger local', 'Jouer au frisbee', 'Camping...dans un vrai camping ou dans le jardin !', 'Promenade en v??lo',
            'Jouer au Badminton dans un parc', 'Laser game', 'Paint ball', 'Concert ou pi??ce de th????tre', 'Ramasser les d??chets dans un parc ou une foret',
            'Partez en road trip pour la journ??e', 'Parc d???attraction, f??te foraine ou parc aquatique', 'P??dicure ou manucure', 'Promenade en bateau ou en barque',
            'Jouez aux fl??chettes', 'Boire un verre dans un bar ?? chats'];

        $participants = [];

        for($i=0; $i < count($activities); $i++){

            $statusCode = '';

            $activity = new Activity();


            $activity
                ->setName($activities[$i])
                ->setStartingDateTime($this->faker->dateTimeBetween('-2 month', '+3 month'))
                ->setDuration($this->faker->numberBetween(5, 120));

            $date = clone $activity->getStartingDateTime();

            $activity
                ->setRegistrationDeadLine($date->modify('-7 day'))
                ->setMaxRegistrationNb($this->faker->numberBetween(2, 25))
                ->setOverview(implode(" ", $this->faker->words(50)));

            $currentDate = new \DateTime;
            $oneMonthBeforeCurrentDate = clone $currentDate;
            $oneMonthBeforeCurrentDate->modify('-1 month');

            if ($activity->getStartingDateTime() < $oneMonthBeforeCurrentDate ) {
                $statusCode = $this->statusArchived;
            } elseif ($activity->getStartingDateTime() < $currentDate) {
                $statusCode = $this->statusFinished;
            } elseif($activity->getRegistrationDeadLine() < $currentDate && $activity->getStartingDateTime() > $currentDate) {
                $statusCode = $this->statusClosed;
            } else {
                $statusCode = $this->faker->randomElement([$this->statusCreated, $this->statusOpened, $this->statusCanceled]);
            }

            foreach ($this->statuses as $status) {
                if ($status->getStatusCode() == $statusCode) {
                    $activity
                        ->setStatus($status);
                }
            }
            $activity
                ->setLocation($this->faker->randomElement($this->locations))
                ->setCampus($this->faker->randomElement($this->campuses))
                ->setUser($this->faker->randomElement($this->users));


            $this->entityManager->persist($activity);
        }
        $this->entityManager->flush();

//        $this->addParticipants();
    }

//    public function addParticipants()
//    {
//
//
//        if ($statusCode != $this->statusCreated && $statusCode != $this->statusCanceled) {
//
//            $numberOfParticipants = $this->faker->numberBetween(2, $activity->getMaxRegistrationNb());
//
//            for ($i = 0; $i < $numberOfParticipants; $i++) {
//
//                $participant = $this->faker->randomElement($this->users);
//                $activity->setUsers($participant);
//            }
//
//            $this->entityManager->persist($activity);
//        }
//        $this->entityManager->flush();
//    }

}
