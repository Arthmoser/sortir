<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
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
    private Generator $faker;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }


    public function load(ObjectManager $manager): void
    {
        $this->addUsers();
    }

    public function addUsers(){

        for($i=0; $i < 10; $i++){

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
                ->setCampus($this->faker->numberBetween(1,3));

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }

}
