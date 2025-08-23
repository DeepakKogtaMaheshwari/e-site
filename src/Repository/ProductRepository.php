<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findAllProducts(): array
    {
        try {
            return $this->createQueryBuilder('p')
                ->orderBy('p.id', 'DESC')
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            // If database error, return sample products for demo
            return $this->getSampleProducts();
        }
    }

    private function getSampleProducts(): array
    {
        // Sample products for demo when database is not available
        $sampleData = [
            [
                'id' => 1,
                'name' => 'B2Battle Precision X1 Ultra Computer Mouse',
                'description' => 'Professional computer mouse with 30,000 DPI PixArt sensor, 0.2ms response time, and lightweight 59g design.',
                'price' => '5999.00',
                'imageUrl' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=500&h=500&fit=crop&auto=format'
            ],
            [
                'id' => 2,
                'name' => 'B2Battle Professional Mechanical Keyboard RGB',
                'description' => 'Professional-grade mechanical keyboard with custom Cherry MX switches and per-key RGB lighting.',
                'price' => '12999.00',
                'imageUrl' => 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=500&h=500&fit=crop&auto=format'
            ],
            [
                'id' => 3,
                'name' => 'B2Battle Professional Audio Headset',
                'description' => 'Professional 7.1 surround sound headset with 50mm titanium drivers and AI-powered noise cancellation.',
                'price' => '8999.00',
                'imageUrl' => 'https://images.unsplash.com/photo-1599669454699-248893623440?w=500&h=500&fit=crop&auto=format'
            ],
            [
                'id' => 4,
                'name' => 'B2Battle Professional 27" 240Hz Monitor',
                'description' => 'Ultra-fast 27" QHD professional monitor with 240Hz refresh rate and 0.5ms response time.',
                'price' => '34999.00',
                'imageUrl' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&h=500&fit=crop&auto=format'
            ],
            [
                'id' => 5,
                'name' => 'B2Battle Elite Ergonomic Office Chair',
                'description' => 'Ergonomic office chair with 4D armrests, memory foam lumbar support, and premium PU leather.',
                'price' => '24999.00',
                'imageUrl' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=500&h=500&fit=crop&auto=format'
            ],
            [
                'id' => 6,
                'name' => 'B2Battle Elite Wireless Controller',
                'description' => 'Professional wireless controller with Hall Effect triggers and 50-hour battery life.',
                'price' => '9999.00',
                'imageUrl' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=500&h=500&fit=crop&auto=format'
            ]
        ];

        $products = [];
        foreach ($sampleData as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setImageUrl($data['imageUrl']);

            // Use reflection to set the ID for demo purposes
            $reflection = new \ReflectionClass($product);
            $idProperty = $reflection->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($product, $data['id']);

            $products[] = $product;
        }

        return $products;
    }

    public function findOneById(int $id): ?Product
    {
        return $this->find($id);
    }
}
