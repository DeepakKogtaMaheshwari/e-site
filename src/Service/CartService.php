<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private SessionInterface $session;
    private ProductRepository $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->session = $requestStack->getSession();
        $this->productRepository = $productRepository;
    }

    /**
     * Add product to cart
     */
    public function addToCart(int $productId, int $quantity = 1): array
    {
        $product = $this->productRepository->find($productId);
        
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found.'
            ];
        }

        $cart = $this->getCart();
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->getPrice(),
                'name' => $product->getName(),
                'image_url' => $product->getImageUrl()
            ];
        }

        $this->saveCart($cart);

        return [
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal()
        ];
    }

    /**
     * Remove product from cart
     */
    public function removeFromCart(int $productId): array
    {
        $cart = $this->getCart();
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveCart($cart);
            
            return [
                'success' => true,
                'message' => 'Product removed from cart.',
                'cart_count' => $this->getCartCount(),
                'cart_total' => $this->getCartTotal()
            ];
        }

        return [
            'success' => false,
            'message' => 'Product not found in cart.'
        ];
    }

    /**
     * Update product quantity in cart
     */
    public function updateQuantity(int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->removeFromCart($productId);
        }

        $cart = $this->getCart();
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            $this->saveCart($cart);
            
            return [
                'success' => true,
                'message' => 'Cart updated successfully.',
                'cart_count' => $this->getCartCount(),
                'cart_total' => $this->getCartTotal()
            ];
        }

        return [
            'success' => false,
            'message' => 'Product not found in cart.'
        ];
    }

    /**
     * Get cart contents
     */
    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    /**
     * Get cart with full product details
     */
    public function getCartWithProducts(): array
    {
        $cart = $this->getCart();
        $cartItems = [];

        foreach ($cart as $productId => $item) {
            $product = $this->productRepository->find($productId);
            
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->getPrice(),
                    'total_price' => (float) $product->getPrice() * $item['quantity']
                ];
            }
        }

        return $cartItems;
    }

    /**
     * Get cart count (total items)
     */
    public function getCartCount(): int
    {
        $cart = $this->getCart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Get cart total amount
     */
    public function getCartTotal(): float
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += (float) $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Get cart summary
     */
    public function getCartSummary(): array
    {
        $subtotal = $this->getCartTotal();
        $tax = $subtotal * 0.18; // 18% GST
        $shipping = $subtotal >= 500 ? 0 : 50; // Free shipping above ₹500
        $total = $subtotal + $tax + $shipping;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'count' => $this->getCartCount(),
            'free_shipping_eligible' => $subtotal >= 500
        ];
    }

    /**
     * Clear cart
     */
    public function clearCart(): void
    {
        $this->session->remove('cart');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        $cart = $this->getCart();
        return empty($cart);
    }

    /**
     * Check if product is in cart
     */
    public function hasProduct(int $productId): bool
    {
        $cart = $this->getCart();
        return isset($cart[$productId]);
    }

    /**
     * Get product quantity in cart
     */
    public function getProductQuantity(int $productId): int
    {
        $cart = $this->getCart();
        return $cart[$productId]['quantity'] ?? 0;
    }

    /**
     * Save cart to session
     */
    private function saveCart(array $cart): void
    {
        $this->session->set('cart', $cart);
    }

    /**
     * Merge guest cart with user cart (when user logs in)
     */
    public function mergeGuestCart(User $user): void
    {
        // For now, we'll keep using session-based cart
        // In future, we can implement database cart for logged users
        // and merge guest cart with user's saved cart
    }

    /**
     * Validate cart items (check if products still exist and prices are current)
     */
    public function validateCart(): array
    {
        $cart = $this->getCart();
        $errors = [];
        $updatedCart = [];

        foreach ($cart as $productId => $item) {
            $product = $this->productRepository->find($productId);
            
            if (!$product) {
                $errors[] = "Product '{$item['name']}' is no longer available and has been removed from your cart.";
                continue;
            }

            // Update price if it has changed
            if ((float) $item['price'] !== (float) $product->getPrice()) {
                $errors[] = "Price for '{$product->getName()}' has been updated from ₹{$item['price']} to ₹{$product->getPrice()}.";
                $item['price'] = $product->getPrice();
            }

            $updatedCart[$productId] = $item;
        }

        if (!empty($errors)) {
            $this->saveCart($updatedCart);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'cart' => $updatedCart
        ];
    }
}
