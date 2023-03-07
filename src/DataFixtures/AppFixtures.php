<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $container;

    public function load(ObjectManager $manager, Container $container=null): void
    {
        $faker = Factory::create('fr_FR');
        $this->addUsers($manager,$faker);
    }

    public function addUsers(ObjectManager $manager){
        $faker = Factory::create('fr_FR');

        $users = Array();
        for($i=0; $i < 20; $i++){

            $users[$i] = new User();
            $hash = $this->container->get('security.password_encoder')->encodePassword($users[$i], 'user');
            $users[$i]
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
                ->setNickname($faker->userName)
                ->setEmail($faker->email)
                ->setPhone($faker->phoneNumber)
                ->setIsAllowed($faker->boolean())
                ->setPassword($faker->password($hash));

            $manager ->persist($users[$i]);
        }
    $manager->flush();
    }

}
