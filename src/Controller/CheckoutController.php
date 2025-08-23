<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout/{id}', name: 'app_checkout', requirements: ['id' => '\d+'])]
    public function checkout(Product $product, Request $request): Response
    {
        $razorpayKeyId = $this->getParameter('razorpay.key_id');

        return $this->render('checkout/index.html.twig', [
            'product' => $product,
            'razorpay_key_id' => $razorpayKeyId,
        ]);
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
