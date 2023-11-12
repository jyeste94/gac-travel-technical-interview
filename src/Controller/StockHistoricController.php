<?php

namespace App\Controller;


use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\StockHistoricRepository;

#[Route('/stock-historic')]
class StockHistoricController extends AbstractController
{

    #[Route('/', name: 'stock_historic_index', methods: ['GET'])]
    public function index(StockHistoricRepository $stockHistoricRepository)
    {
        $stockHistorics = $stockHistoricRepository->findAll();

        return $this->render('stock_historic/index.html.twig', [
            'stock_historics' => $stockHistorics,
        ]);
    }


    #[Route('/product/{id}/stock-historic', name: 'product_stock_historic', methods: ['GET'])]
    public function StockHistoricProduct(Product $product, StockHistoricRepository $stockHistoricRepository)
    {

        $stockHistorics = $stockHistoricRepository->findBy(['product_id' => $product]);

        return $this->render('stock_historic/show.html.twig', [
            'stockHistorics' => $stockHistorics,
            'product' => $product
        ]);
    }





}