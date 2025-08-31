<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $productRepository, CartService $cartService): Response
    {
        $products = $productRepository->findAllProducts();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'cart_service' => $cartService,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(Product $product, CartService $cartService): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'cart_service' => $cartService,
        ]);
    }

    #[Route('/products/search', name: 'app_products_search')]
    public function search(Request $request, ProductRepository $productRepository, CartService $cartService): Response
    {
        $query = $request->query->get('q', '');
        $products = [];

        if ($query) {
            $products = $productRepository->searchProducts($query);
        }

        return $this->render('product/search.html.twig', [
            'products' => $products,
            'query' => $query,
            'cart_service' => $cartService,
        ]);
    }
}
