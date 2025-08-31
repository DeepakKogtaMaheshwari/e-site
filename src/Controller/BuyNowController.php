<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BuyNowController extends AbstractController
{
    #[Route('/buy-now/{id}', name: 'app_buy_now', requirements: ['id' => '\d+'])]
    public function buyNow(Product $product, Request $request, CartService $cartService): Response
    {
        // Check if user is logged in
        if (!$this->getUser()) {
            // Store the buy now intent in session
            $request->getSession()->set('buy_now_product_id', $product->getId());
            $request->getSession()->set('buy_now_quantity', $request->query->get('quantity', 1));
            
            $this->addFlash('info', 'Please login to purchase this product.');
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        // Check if user's email is verified
        if (!$user->canLogin()) {
            $this->addFlash('error', 'Please verify your email address before making a purchase.');
            return $this->redirectToRoute('app_verify_email');
        }

        $quantity = (int) $request->query->get('quantity', 1);
        
        if ($quantity <= 0) {
            $this->addFlash('error', 'Invalid quantity specified.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Clear existing cart and add this product
        $cartService->clearCart();
        $result = $cartService->addToCart($product->getId(), $quantity);

        if ($result['success']) {
            // Redirect directly to checkout
            return $this->redirectToRoute('app_checkout');
        } else {
            $this->addFlash('error', $result['message']);
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }
    }

    #[Route('/process-buy-now-after-login', name: 'app_process_buy_now_after_login')]
    public function processBuyNowAfterLogin(Request $request, CartService $cartService): Response
    {
        // Check if user is logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        // Check if user's email is verified
        if (!$user->canLogin()) {
            $this->addFlash('error', 'Please verify your email address before making a purchase.');
            return $this->redirectToRoute('app_verify_email');
        }

        // Get buy now intent from session
        $productId = $request->getSession()->get('buy_now_product_id');
        $quantity = $request->getSession()->get('buy_now_quantity', 1);

        if (!$productId) {
            $this->addFlash('error', 'No product selected for purchase.');
            return $this->redirectToRoute('app_products');
        }

        // Clear the session data
        $request->getSession()->remove('buy_now_product_id');
        $request->getSession()->remove('buy_now_quantity');

        // Clear existing cart and add the product
        $cartService->clearCart();
        $result = $cartService->addToCart($productId, $quantity);

        if ($result['success']) {
            $this->addFlash('success', 'Product added to cart. Proceed with checkout.');
            return $this->redirectToRoute('app_checkout');
        } else {
            $this->addFlash('error', $result['message']);
            return $this->redirectToRoute('app_products');
        }
    }
}
