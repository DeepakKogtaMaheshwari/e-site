<?php

namespace App\Service;

use App\Entity\EmailVerification;
use App\Repository\EmailVerificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Psr\Log\LoggerInterface;

class EmailVerificationService
{
    private EntityManagerInterface $entityManager;
    private EmailVerificationRepository $otpRepository;
    private MailerInterface $mailer;
    private RequestStack $requestStack;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        EmailVerificationRepository $otpRepository,
        MailerInterface $mailer,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->otpRepository = $otpRepository;
        $this->mailer = $mailer;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * Generate and send OTP to email
     */
    public function sendOtp(string $email, string $type = 'registration', array $context = []): array
    {
        try {
            // Rate limiting checks
            $rateLimitResult = $this->checkRateLimit($email);
            if (!$rateLimitResult['allowed']) {
                return [
                    'success' => false,
                    'message' => $rateLimitResult['message'],
                    'retry_after' => $rateLimitResult['retry_after'] ?? null
                ];
            }

            // Invalidate existing OTPs for this email and type
            $this->otpRepository->invalidateExistingOtps($email, $type);

            // Generate new OTP
            $otpCode = $this->generateOtpCode();
            
            // Create OTP record
            $otp = new EmailVerification();
            $otp->setEmail($email)
                ->setOtpCode($otpCode)
                ->setType($type)
                ->setIpAddress($this->getClientIp());

            $this->entityManager->persist($otp);
            $this->entityManager->flush();

            // Send email
            $emailSent = $this->sendOtpEmail($email, $otpCode, $type, $context);

            if ($emailSent) {
                $this->logger->info('OTP sent successfully', [
                    'email' => $email,
                    'type' => $type,
                    'ip' => $this->getClientIp()
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP sent successfully to your email address.',
                    'expires_in' => 600, // 10 minutes
                    'otp_id' => $otp->getId()
                ];
            } else {
                // Remove OTP record if email failed
                $this->entityManager->remove($otp);
                $this->entityManager->flush();

                return [
                    'success' => false,
                    'message' => 'Failed to send OTP email. Please try again.'
                ];
            }

        } catch (\Exception $e) {
            $this->logger->error('Failed to send OTP', [
                'email' => $email,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while sending OTP. Please try again.'
            ];
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $email, string $code, string $type = 'registration'): array
    {
        try {
            $otp = $this->otpRepository->findByEmailAndCode($email, $code, $type);

            if (!$otp) {
                return [
                    'success' => false,
                    'message' => 'Invalid OTP code.'
                ];
            }

            // Increment attempts
            $otp->incrementAttempts();
            $this->entityManager->flush();

            if (!$otp->isValid()) {
                if ($otp->isExpired()) {
                    return [
                        'success' => false,
                        'message' => 'OTP code has expired. Please request a new one.'
                    ];
                }

                if ($otp->isUsed()) {
                    return [
                        'success' => false,
                        'message' => 'OTP code has already been used.'
                    ];
                }

                if ($otp->getAttempts() >= 5) {
                    return [
                        'success' => false,
                        'message' => 'Too many failed attempts. Please request a new OTP.'
                    ];
                }
            }

            // Mark as used
            $otp->markAsUsed();
            $this->entityManager->flush();

            $this->logger->info('OTP verified successfully', [
                'email' => $email,
                'type' => $type,
                'ip' => $this->getClientIp()
            ]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully.'
            ];

        } catch (\Exception $e) {
            $this->logger->error('Failed to verify OTP', [
                'email' => $email,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while verifying OTP. Please try again.'
            ];
        }
    }

    /**
     * Check if user has valid pending OTP
     */
    public function hasPendingOtp(string $email, string $type = 'registration'): bool
    {
        $otp = $this->otpRepository->findValidOtpByEmail($email, $type);
        return $otp !== null;
    }

    /**
     * Get remaining time for pending OTP
     */
    public function getPendingOtpInfo(string $email, string $type = 'registration'): ?array
    {
        $otp = $this->otpRepository->findValidOtpByEmail($email, $type);
        
        if (!$otp) {
            return null;
        }

        return [
            'remaining_time' => $otp->getRemainingTime(),
            'remaining_time_formatted' => $otp->getRemainingTimeFormatted(),
            'attempts' => $otp->getAttempts(),
            'max_attempts' => 5
        ];
    }

    /**
     * Generate 6-digit OTP code
     */
    private function generateOtpCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP email
     */
    private function sendOtpEmail(string $email, string $otpCode, string $type, array $context = []): bool
    {
        try {
            $subject = match ($type) {
                'registration' => 'Verify Your B2Battle Account',
                'password_reset' => 'Reset Your B2Battle Password',
                default => 'B2Battle Verification Code'
            };

            $template = match ($type) {
                'registration' => 'emails/otp_registration.html.twig',
                'password_reset' => 'emails/otp_password_reset.html.twig',
                default => 'emails/otp_generic.html.twig'
            };

            $email = (new TemplatedEmail())
                ->from(new Address('noreply@b2battle.com', 'B2Battle Electronics'))
                ->to($email)
                ->subject($subject)
                ->htmlTemplate($template)
                ->context(array_merge([
                    'otp_code' => $otpCode,
                    'expires_in_minutes' => 10,
                    'type' => $type
                ], $context));

            $this->mailer->send($email);
            return true;

        } catch (\Exception $e) {
            $this->logger->error('Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check rate limiting
     */
    private function checkRateLimit(string $email): array
    {
        // Check email-based rate limiting (max 3 OTPs per 5 minutes)
        $emailRequests = $this->otpRepository->countRecentOtpRequests($email, 5);
        if ($emailRequests >= 3) {
            return [
                'allowed' => false,
                'message' => 'Too many OTP requests. Please wait 5 minutes before requesting again.',
                'retry_after' => 300 // 5 minutes
            ];
        }

        // Check IP-based rate limiting (max 10 OTPs per 5 minutes)
        $ipRequests = $this->otpRepository->countRecentOtpRequestsByIp($this->getClientIp(), 5);
        if ($ipRequests >= 10) {
            return [
                'allowed' => false,
                'message' => 'Too many requests from your IP. Please wait 5 minutes.',
                'retry_after' => 300 // 5 minutes
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Get client IP address
     */
    private function getClientIp(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        
        if (!$request) {
            return '127.0.0.1';
        }

        return $request->getClientIp() ?? '127.0.0.1';
    }

    /**
     * Clean up expired and used OTPs
     */
    public function cleanup(): array
    {
        $expiredCount = $this->otpRepository->cleanupExpiredOtps();
        $usedCount = $this->otpRepository->cleanupUsedOtps();

        return [
            'expired_removed' => $expiredCount,
            'used_removed' => $usedCount,
            'total_removed' => $expiredCount + $usedCount
        ];
    }
}
