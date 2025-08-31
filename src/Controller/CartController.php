<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        $cartItems = $cartService->getCartWithProducts();
        $cartSummary = $cartService->getCartSummary();
        $validation = $cartService->validateCart();

        return $this->render('cart/index.html.twig', [
            'cart_items' => $cartItems,
            'cart_summary' => $cartSummary,
            'validation_errors' => $validation['errors'] ?? [],
        ]);
    }

    #[Route('/add/{productId}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $productId, Request $request, CartService $cartService): JsonResponse
    {
        $quantity = (int) $request->request->get('quantity', 1);
        
        if ($quantity <= 0) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid quantity.'
            ], 400);
        }

        $result = $cartService->addToCart($productId, $quantity);
        
        return new JsonResponse($result);
    }

    #[Route('/remove/{productId}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $productId, CartService $cartService): JsonResponse
    {
        $result = $cartService->removeFromCart($productId);
        
        return new JsonResponse($result);
    }

    #[Route('/update/{productId}', name: 'app_cart_update', methods: ['POST'])]
    public function update(int $productId, Request $request, CartService $cartService): JsonResponse
    {
        $quantity = (int) $request->request->get('quantity', 1);
        
        $result = $cartService->updateQuantity($productId, $quantity);
        
        return new JsonResponse($result);
    }

    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(CartService $cartService): JsonResponse
    {
        $cartService->clearCart();
        
        return new JsonResponse([
            'success' => true,
            'message' => 'Cart cleared successfully.',
            'cart_count' => 0,
            'cart_total' => 0
        ]);
    }

    #[Route('/count', name: 'app_cart_count', methods: ['GET'])]
    public function count(CartService $cartService): JsonResponse
    {
        return new JsonResponse([
            'count' => $cartService->getCartCount(),
            'total' => $cartService->getCartTotal()
        ]);
    }

    #[Route('/summary', name: 'app_cart_summary', methods: ['GET'])]
    public function summary(CartService $cartService): JsonResponse
    {
        return new JsonResponse($cartService->getCartSummary());
    }
}
