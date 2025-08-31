<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailVerificationController extends AbstractController
{
    #[Route('/verify-email', name: 'app_verify_email')]
    public function verifyEmail(Request $request, EmailVerificationService $emailVerificationService): Response
    {
        // Get email from session (set during registration)
        $email = $request->getSession()->get('pending_verification_email');
        
        if (!$email) {
            $this->addFlash('error', 'No pending email verification found. Please register again.');
            return $this->redirectToRoute('app_register');
        }

        // Check if there's a pending OTP
        $pendingOtpInfo = $emailVerificationService->getPendingOtpInfo($email, 'registration');

        return $this->render('email_verification/verify.html.twig', [
            'email' => $email,
            'pending_otp_info' => $pendingOtpInfo,
        ]);
    }

    #[Route('/verify-email/submit', name: 'app_verify_email_submit', methods: ['POST'])]
    public function submitVerification(
        Request $request, 
        EmailVerificationService $emailVerificationService,
        EntityManagerInterface $entityManager
    ): Response {
        $email = $request->getSession()->get('pending_verification_email');
        $otpCode = $request->request->get('otp_code');

        if (!$email) {
            $this->addFlash('error', 'No pending email verification found. Please register again.');
            return $this->redirectToRoute('app_register');
        }

        if (!$otpCode) {
            $this->addFlash('error', 'Please enter the verification code.');
            return $this->redirectToRoute('app_verify_email');
        }

        // Verify OTP
        $verificationResult = $emailVerificationService->verifyOtp($email, $otpCode, 'registration');

        if ($verificationResult['success']) {
            // Find user and mark email as verified
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if ($user) {
                $user->markEmailAsVerified();
                $entityManager->flush();

                // Clear session
                $request->getSession()->remove('pending_verification_email');

                $this->addFlash('success', 'Email verified successfully! You can now login to your account.');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'User account not found. Please register again.');
                return $this->redirectToRoute('app_register');
            }
        } else {
            $this->addFlash('error', $verificationResult['message']);
            return $this->redirectToRoute('app_verify_email');
        }
    }

    #[Route('/verify-email/resend', name: 'app_verify_email_resend', methods: ['POST'])]
    public function resendVerification(
        Request $request, 
        EmailVerificationService $emailVerificationService,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $email = $request->getSession()->get('pending_verification_email');

        if (!$email) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No pending email verification found.'
            ], 400);
        }

        // Find user for context
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'User account not found.'
            ], 404);
        }

        // Send new OTP
        $otpResult = $emailVerificationService->sendOtp(
            $email, 
            'registration',
            [
                'user_name' => $user->getFullName(),
                'user_email' => $user->getEmail()
            ]
        );

        return new JsonResponse($otpResult);
    }

    #[Route('/verify-email/check-status', name: 'app_verify_email_status', methods: ['GET'])]
    public function checkStatus(Request $request, EmailVerificationService $emailVerificationService): JsonResponse
    {
        $email = $request->getSession()->get('pending_verification_email');

        if (!$email) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No pending email verification found.'
            ], 400);
        }

        $pendingOtpInfo = $emailVerificationService->getPendingOtpInfo($email, 'registration');

        if (!$pendingOtpInfo) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No valid OTP found. Please request a new one.',
                'expired' => true
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'remaining_time' => $pendingOtpInfo['remaining_time'],
            'remaining_time_formatted' => $pendingOtpInfo['remaining_time_formatted'],
            'attempts' => $pendingOtpInfo['attempts'],
            'max_attempts' => $pendingOtpInfo['max_attempts']
        ]);
    }

    #[Route('/verify-email/cancel', name: 'app_verify_email_cancel')]
    public function cancelVerification(Request $request): Response
    {
        // Clear session
        $request->getSession()->remove('pending_verification_email');
        
        $this->addFlash('info', 'Email verification cancelled. You can register again if needed.');
        return $this->redirectToRoute('app_register');
    }
}
