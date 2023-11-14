<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpClient\HttpClient;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://fakestoreapi.com/products/categories');

        $categories = $response->toArray();


        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName( $categoryData );

            $manager->persist($category);
        }

        $manager->flush();
    }
}