<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Find orders by user
     */
    public function findByUser(User $user, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find order by order number
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.orderNumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find orders by status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get recent orders
     */
    public function findRecentOrders(int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get orders by date range
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.createdAt >= :startDate')
            ->andWhere('o.createdAt <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get order statistics
     */
    public function getOrderStats(): array
    {
        $qb = $this->createQueryBuilder('o');
        
        return [
            'total_orders' => $qb->select('COUNT(o.id)')->getQuery()->getSingleScalarResult(),
            'pending_orders' => $qb->select('COUNT(o.id)')->where('o.status = :status')->setParameter('status', 'pending')->getQuery()->getSingleScalarResult(),
            'confirmed_orders' => $qb->select('COUNT(o.id)')->where('o.status = :status')->setParameter('status', 'confirmed')->getQuery()->getSingleScalarResult(),
            'shipped_orders' => $qb->select('COUNT(o.id)')->where('o.status = :status')->setParameter('status', 'shipped')->getQuery()->getSingleScalarResult(),
            'delivered_orders' => $qb->select('COUNT(o.id)')->where('o.status = :status')->setParameter('status', 'delivered')->getQuery()->getSingleScalarResult(),
            'cancelled_orders' => $qb->select('COUNT(o.id)')->where('o.status = :status')->setParameter('status', 'cancelled')->getQuery()->getSingleScalarResult(),
        ];
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.totalAmount)')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('statuses', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Get monthly revenue
     */
    public function getMonthlyRevenue(int $year, int $month): float
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = clone $startDate;
        $endDate->modify('last day of this month')->setTime(23, 59, 59);

        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.totalAmount)')
            ->andWhere('o.createdAt >= :startDate')
            ->andWhere('o.createdAt <= :endDate')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('statuses', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Find user's order by order number
     */
    public function findUserOrderByNumber(User $user, string $orderNumber): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->andWhere('o.orderNumber = :orderNumber')
            ->setParameter('user', $user)
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Count user orders
     */
    public function countUserOrders(User $user): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get user's total spent
     */
    public function getUserTotalSpent(User $user): float
    {
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.totalAmount)')
            ->andWhere('o.user = :user')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('statuses', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}
