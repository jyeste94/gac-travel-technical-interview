<?php

namespace App\DataFixtures;

use App\Controller\CategoryController;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpClient\HttpClient;

class ProductFixtures extends Fixture
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://fakestoreapi.com/products');

        $products = $response->toArray();

        foreach ($products as $productData) {
            $product = new Product();
            $product->setCategoryId( $this->entityManager->getRepository(Category::class)->findOneByName( $productData['category']  ) );
            $product->setName($productData['title']);
            $product->setStock(0);

            $manager->persist($product);
        }

        $manager->flush();
    }
}