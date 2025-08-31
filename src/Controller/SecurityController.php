<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Service\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        EntityManagerInterface $entityManager,
        EmailVerificationService $emailVerificationService
    ): Response {
        // Redirect if user is already logged in
        if ($this->getUser()) {
            // Check if user was trying to buy something
            if ($request->getSession()->has('buy_now_product_id')) {
                return $this->redirectToRoute('app_process_buy_now_after_login');
            }

            // Check if user was trying to checkout
            if ($request->getSession()->get('checkout_after_login')) {
                $request->getSession()->remove('checkout_after_login');
                return $this->redirectToRoute('app_checkout');
            }

            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Check if login failed due to unverified email
        if ($error && $lastUsername) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $lastUsername]);

            if ($user && !$user->isEmailVerified()) {
                // Store email in session for verification
                $request->getSession()->set('pending_verification_email', $user->getEmail());

                // Check if user has pending OTP or needs new one
                if (!$emailVerificationService->hasPendingOtp($user->getEmail(), 'registration')) {
                    // Send new OTP
                    $otpResult = $emailVerificationService->sendOtp(
                        $user->getEmail(),
                        'registration',
                        [
                            'user_name' => $user->getFullName(),
                            'user_email' => $user->getEmail()
                        ]
                    );

                    if ($otpResult['success']) {
                        $this->addFlash('info', 'Your email is not verified. We\'ve sent a new verification code to your email.');
                    } else {
                        $this->addFlash('error', 'Your email is not verified. Please contact support for assistance.');
                    }
                } else {
                    $this->addFlash('info', 'Your email is not verified. Please check your email for the verification code.');
                }

                return $this->redirectToRoute('app_verify_email');
            }
        }

        // Create the login form
        $form = $this->createForm(LoginFormType::class, [
            'email' => $lastUsername,
        ]);

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
