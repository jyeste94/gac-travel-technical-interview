<?php

namespace App\Controller;


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



}