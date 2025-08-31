<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password change if provided
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );
            }

            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated successfully!');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'profileForm' => $form->createView(),
        ]);
    }

    #[Route('/orders', name: 'app_profile_orders')]
    public function orders(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $orderRepository = $entityManager->getRepository(\App\Entity\Order::class);
        $orders = $orderRepository->findByUser($user, 10); // Last 10 orders
        $totalSpent = $orderRepository->getUserTotalSpent($user);
        $totalOrders = $orderRepository->countUserOrders($user);

        return $this->render('profile/orders.html.twig', [
            'user' => $user,
            'orders' => $orders,
            'total_spent' => $totalSpent,
            'total_orders' => $totalOrders,
        ]);
    }

    #[Route('/addresses', name: 'app_profile_addresses')]
    public function addresses(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile/addresses.html.twig', [
            'user' => $user,
        ]);
    }
}
