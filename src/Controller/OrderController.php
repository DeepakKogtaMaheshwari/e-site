<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/orders')]
#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_orders')]
    public function index(OrderRepository $orderRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $orders = $orderRepository->findByUser($user);
        $totalSpent = $orderRepository->getUserTotalSpent($user);
        $totalOrders = $orderRepository->countUserOrders($user);

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
            'total_spent' => $totalSpent,
            'total_orders' => $totalOrders,
        ]);
    }

    #[Route('/{orderNumber}', name: 'app_order_detail')]
    public function detail(string $orderNumber, OrderRepository $orderRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $order = $orderRepository->findUserOrderByNumber($user, $orderNumber);
        
        if (!$order) {
            $this->addFlash('error', 'Order not found.');
            return $this->redirectToRoute('app_orders');
        }

        return $this->render('orders/detail.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/confirmation/{orderNumber}', name: 'app_order_confirmation')]
    public function confirmation(string $orderNumber, OrderRepository $orderRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $order = $orderRepository->findUserOrderByNumber($user, $orderNumber);
        
        if (!$order) {
            $this->addFlash('error', 'Order not found.');
            return $this->redirectToRoute('app_orders');
        }

        return $this->render('orders/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{orderNumber}/cancel', name: 'app_order_cancel', methods: ['POST'])]
    public function cancel(string $orderNumber, Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $order = $orderRepository->findUserOrderByNumber($user, $orderNumber);

        if (!$order) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => false, 'message' => 'Order not found']);
            }
            $this->addFlash('error', 'Order not found.');
            return $this->redirectToRoute('app_orders');
        }

        if (!$order->canBeCancelled()) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => false, 'message' => 'This order cannot be cancelled']);
            }
            $this->addFlash('error', 'This order cannot be cancelled.');
            return $this->redirectToRoute('app_order_detail', ['orderNumber' => $orderNumber]);
        }

        $order->setStatus('cancelled');
        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(['success' => true, 'message' => 'Order has been cancelled successfully']);
        }

        $this->addFlash('success', 'Order has been cancelled successfully.');
        return $this->redirectToRoute('app_order_detail', ['orderNumber' => $orderNumber]);
    }
}
