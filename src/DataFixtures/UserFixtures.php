<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://fakestoreapi.com/users');

        $users = $response->toArray();


        foreach ($users as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);

            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $userData['password']
                )
            );

            $user->setActive(true);

            $manager->persist($user);
        }

        $manager->flush();
    }

}