<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\StockHistoric;
use App\Form\ProductStockType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('No tienes permiso para ver esta página');
        }

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('No tienes permiso para ver esta página');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if( $product->getStock() == null ){
                $product->setStock(0);
            }

            $productRepository->add($product);
            $this->addFlash('newProductSuccess', 'Producto registrado con éxito.');

            return $this->redirectToRoute('app_product_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('No tienes permiso para ver esta página');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('No tienes permiso para ver esta página');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product);
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('No tienes permiso para ver esta página');
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/edit-stock", name="product_edit_stock", methods={"GET", "POST"})
     */
    public function editStock(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductStockType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stockChange = $form->get('stock')->getData();


            if ($stockChange < 0 && abs($stockChange) > $product->getStock()) {
                $this->addFlash('error', 'No puedes eliminar más stock del existente.');
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $stockHistoric = new StockHistoric();
                $stockHistoric->setProductId($product);
                $stockHistoric->setStock($stockChange);
                $stockHistoric->setCreatedAt(new \DateTimeImmutable());
                $stockHistoric->setUserid($this->getUser());

                $entityManager->persist($stockHistoric);


                $product->setStock($product->getStock() + $stockChange);

                $entityManager->persist($product);
                $entityManager->flush($product);

                $this->addFlash('success', 'El stock del producto ha sido actualizado.');
            }

            return $this->redirectToRoute('app_product_index');
        }


        return $this->render('product/edit_stock.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

}
