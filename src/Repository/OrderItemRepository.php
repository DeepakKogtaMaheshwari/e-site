<?php

namespace App\Repository;

use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    /**
     * Find items by product
     */
    public function findByProduct(Product $product): array
    {
        return $this->createQueryBuilder('oi')
            ->andWhere('oi.product = :product')
            ->setParameter('product', $product)
            ->orderBy('oi.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get total quantity sold for a product
     */
    public function getTotalQuantitySold(Product $product): int
    {
        $result = $this->createQueryBuilder('oi')
            ->select('SUM(oi.quantity)')
            ->join('oi.order', 'o')
            ->andWhere('oi.product = :product')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('product', $product)
            ->setParameter('statuses', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts(int $limit = 10): array
    {
        return $this->createQueryBuilder('oi')
            ->select('oi.product, oi.productName, SUM(oi.quantity) as totalSold')
            ->join('oi.order', 'o')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('statuses', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->groupBy('oi.product')
            ->orderBy('totalSold', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get revenue by product
     */
    public function getRevenueByProduct(Product $product): float
    {
        $result = $this->createQueryBuilder('oi')
            ->select('SUM(oi.totalPrice)')
            ->join('oi.order', 'o')
            ->andWhere('oi.product = :product')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('product', $product)
            ->setParameter('statuses', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}
