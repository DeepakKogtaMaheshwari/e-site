<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/checkout')]
class CheckoutController extends AbstractController
{
    #[Route('/', name: 'app_checkout')]
    public function index(CartService $cartService): Response
    {
        // Check if user is logged in
        if (!$this->getUser()) {
            $this->addFlash('info', 'Please login or create an account to proceed with checkout.');
            return $this->redirectToRoute('app_login');
        }

        // Check if user is logged in and email is verified
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canLogin()) {
            $this->addFlash('error', 'Please verify your email address before placing an order.');
            return $this->redirectToRoute('app_verify_email');
        }

        // Check if cart is empty
        if ($cartService->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty. Add some products before checkout.');
            return $this->redirectToRoute('app_products');
        }

        // Validate cart
        $validation = $cartService->validateCart();
        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                $this->addFlash('warning', $error);
            }
        }

        $cartItems = $cartService->getCartWithProducts();
        $cartSummary = $cartService->getCartSummary();

        return $this->render('checkout/index.html.twig', [
            'user' => $user,
            'cart_items' => $cartItems,
            'cart_summary' => $cartSummary,
        ]);
    }

    #[Route('/place-order', name: 'app_checkout_place_order', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function placeOrder(
        Request $request,
        CartService $cartService,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // Verify user can place orders
        if (!$user->canLogin()) {
            $this->addFlash('error', 'Please verify your email address before placing an order.');
            return $this->redirectToRoute('app_verify_email');
        }

        // Check if cart is empty
        if ($cartService->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty.');
            return $this->redirectToRoute('app_cart');
        }

        // Validate cart one more time
        $validation = $cartService->validateCart();
        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                $this->addFlash('error', $error);
            }
            return $this->redirectToRoute('app_checkout');
        }

        try {
            // Create order
            $order = new Order();
            $order->setUser($user);
            $order->copyShippingFromUser($user);

            // Set payment method
            $paymentMethod = $request->request->get('payment_method', 'cod');
            $order->setPaymentMethod($paymentMethod);

            // Add order notes if provided
            $notes = $request->request->get('notes');
            if ($notes) {
                $order->setNotes($notes);
            }

            // Add cart items to order
            $cartItems = $cartService->getCartWithProducts();
            foreach ($cartItems as $cartItem) {
                $orderItem = OrderItem::createFromProduct($cartItem['product'], $cartItem['quantity']);
                $order->addOrderItem($orderItem);
            }

            // Calculate totals
            $order->calculateTotals();

            // Save order
            $entityManager->persist($order);
            $entityManager->flush();

            // Clear cart
            $cartService->clearCart();

            // Success message
            $this->addFlash('success', 'Your order has been placed successfully! Order Number: ' . $order->getOrderNumber());

            return $this->redirectToRoute('app_order_confirmation', ['orderNumber' => $order->getOrderNumber()]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while placing your order. Please try again.');
            return $this->redirectToRoute('app_checkout');
        }
    }

    #[Route('/login-required', name: 'app_checkout_login_required')]
    public function loginRequired(Request $request): Response
    {
        // Store the intended checkout URL in session
        $request->getSession()->set('checkout_after_login', true);

        $this->addFlash('info', 'Please login or create an account to proceed with checkout.');

        return $this->render('checkout/login_required.html.twig');
    }

    #[Route('/payment-success', name: 'app_payment_success')]
    public function paymentSuccess(Request $request): Response
    {
        // In a real application, you would verify the payment with Razorpay
        // using the payment_id, order_id, and signature from the request

        $paymentId = $request->query->get('payment_id');
        $orderId = $request->query->get('order_id');

        return $this->render('checkout/success.html.twig', [
            'payment_id' => $paymentId,
            'order_id' => $orderId,
        ]);
    }

    #[Route('/payment-failed', name: 'app_payment_failed')]
    public function paymentFailed(Request $request): Response
    {
        $error = $request->query->get('error', 'Payment failed. Please try again.');

        return $this->render('checkout/failed.html.twig', [
            'error' => $error,
        ]);
    }
}
