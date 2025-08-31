<?php

namespace App\Repository;

use App\Entity\EmailVerification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailVerification>
 */
class EmailVerificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerification::class);
    }

    /**
     * Find the latest valid OTP for an email and type
     */
    public function findValidOtpByEmail(string $email, string $type = 'registration'): ?EmailVerification
    {
        return $this->createQueryBuilder('ev')
            ->andWhere('ev.email = :email')
            ->andWhere('ev.type = :type')
            ->andWhere('ev.isUsed = false')
            ->andWhere('ev.expiresAt > :now')
            ->andWhere('ev.attempts < 5')
            ->setParameter('email', $email)
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('ev.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find OTP by email and code
     */
    public function findByEmailAndCode(string $email, string $code, string $type = 'registration'): ?EmailVerification
    {
        return $this->createQueryBuilder('ev')
            ->andWhere('ev.email = :email')
            ->andWhere('ev.otpCode = :code')
            ->andWhere('ev.type = :type')
            ->setParameter('email', $email)
            ->setParameter('code', $code)
            ->setParameter('type', $type)
            ->orderBy('ev.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Clean up expired OTPs
     */
    public function cleanupExpiredOtps(): int
    {
        return $this->createQueryBuilder('ev')
            ->delete()
            ->where('ev.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }

    /**
     * Clean up used OTPs older than 24 hours
     */
    public function cleanupUsedOtps(): int
    {
        $yesterday = new \DateTimeImmutable('-24 hours');
        
        return $this->createQueryBuilder('ev')
            ->delete()
            ->where('ev.isUsed = true')
            ->andWhere('ev.usedAt < :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->execute();
    }

    /**
     * Count recent OTP requests for rate limiting
     */
    public function countRecentOtpRequests(string $email, int $minutes = 5): int
    {
        $since = new \DateTimeImmutable("-{$minutes} minutes");
        
        return $this->createQueryBuilder('ev')
            ->select('COUNT(ev.id)')
            ->andWhere('ev.email = :email')
            ->andWhere('ev.createdAt > :since')
            ->setParameter('email', $email)
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count recent OTP requests from IP for rate limiting
     */
    public function countRecentOtpRequestsByIp(string $ipAddress, int $minutes = 5): int
    {
        $since = new \DateTimeImmutable("-{$minutes} minutes");
        
        return $this->createQueryBuilder('ev')
            ->select('COUNT(ev.id)')
            ->andWhere('ev.ipAddress = :ip')
            ->andWhere('ev.createdAt > :since')
            ->setParameter('ip', $ipAddress)
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Invalidate all existing OTPs for an email
     */
    public function invalidateExistingOtps(string $email, string $type = 'registration'): int
    {
        return $this->createQueryBuilder('ev')
            ->update()
            ->set('ev.isUsed', 'true')
            ->set('ev.usedAt', ':now')
            ->where('ev.email = :email')
            ->andWhere('ev.type = :type')
            ->andWhere('ev.isUsed = false')
            ->setParameter('email', $email)
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }

    /**
     * Get OTP statistics for admin
     */
    public function getOtpStats(): array
    {
        $qb = $this->createQueryBuilder('ev');
        
        return [
            'total_sent' => $qb->select('COUNT(ev.id)')->getQuery()->getSingleScalarResult(),
            'total_used' => $qb->select('COUNT(ev.id)')->where('ev.isUsed = true')->getQuery()->getSingleScalarResult(),
            'total_expired' => $qb->select('COUNT(ev.id)')->where('ev.expiresAt < :now')->setParameter('now', new \DateTimeImmutable())->getQuery()->getSingleScalarResult(),
            'total_pending' => $qb->select('COUNT(ev.id)')->where('ev.isUsed = false')->andWhere('ev.expiresAt > :now')->setParameter('now', new \DateTimeImmutable())->getQuery()->getSingleScalarResult(),
        ];
    }
}
