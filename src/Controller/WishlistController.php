<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/wishlist')]
class WishlistController extends AbstractController
{
    #[Route('/', name: 'app_wishlist')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        // Get wishlist from session or create empty array
        $wishlistIds = $request->getSession()->get('wishlist', []);
        
        $products = [];
        if (!empty($wishlistIds)) {
            $products = $productRepository->findBy(['id' => $wishlistIds]);
        }

        return $this->render('wishlist/index.html.twig', [
            'products' => $products,
            'wishlist_count' => count($products),
        ]);
    }

    #[Route('/add/{id}', name: 'app_wishlist_add', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function add(int $id, Request $request, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        
        if (!$product) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => false, 'message' => 'Product not found']);
            }
            $this->addFlash('error', 'Product not found');
            return $this->redirectToRoute('app_products');
        }

        $wishlist = $request->getSession()->get('wishlist', []);
        
        if (!in_array($id, $wishlist)) {
            $wishlist[] = $id;
            $request->getSession()->set('wishlist', $wishlist);
            
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true, 
                    'message' => 'Added to wishlist',
                    'wishlist_count' => count($wishlist)
                ]);
            }
            $this->addFlash('success', 'Product added to wishlist');
        } else {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => false, 'message' => 'Product already in wishlist']);
            }
            $this->addFlash('info', 'Product already in wishlist');
        }

        return $this->redirectToRoute('app_wishlist');
    }

    #[Route('/remove/{id}', name: 'app_wishlist_remove', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function remove(int $id, Request $request): Response
    {
        $wishlist = $request->getSession()->get('wishlist', []);
        $key = array_search($id, $wishlist);
        
        if ($key !== false) {
            unset($wishlist[$key]);
            $wishlist = array_values($wishlist); // Re-index array
            $request->getSession()->set('wishlist', $wishlist);
            
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true, 
                    'message' => 'Removed from wishlist',
                    'wishlist_count' => count($wishlist)
                ]);
            }
            $this->addFlash('success', 'Product removed from wishlist');
        } else {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => false, 'message' => 'Product not in wishlist']);
            }
            $this->addFlash('error', 'Product not in wishlist');
        }

        return $this->redirectToRoute('app_wishlist');
    }

    #[Route('/toggle/{id}', name: 'app_wishlist_toggle', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function toggle(int $id, Request $request, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        
        if (!$product) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => false, 'message' => 'Product not found']);
            }
            return $this->redirectToRoute('app_products');
        }

        $wishlist = $request->getSession()->get('wishlist', []);
        $key = array_search($id, $wishlist);
        
        if ($key !== false) {
            // Remove from wishlist
            unset($wishlist[$key]);
            $wishlist = array_values($wishlist);
            $message = 'Removed from wishlist';
            $inWishlist = false;
        } else {
            // Add to wishlist
            $wishlist[] = $id;
            $message = 'Added to wishlist';
            $inWishlist = true;
        }
        
        $request->getSession()->set('wishlist', $wishlist);
        
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'message' => $message,
                'in_wishlist' => $inWishlist,
                'wishlist_count' => count($wishlist)
            ]);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_wishlist');
    }

    #[Route('/clear', name: 'app_wishlist_clear', methods: ['POST'])]
    public function clear(Request $request): Response
    {
        $request->getSession()->remove('wishlist');
        
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'message' => 'Wishlist cleared',
                'wishlist_count' => 0
            ]);
        }
        
        $this->addFlash('success', 'Wishlist cleared');
        return $this->redirectToRoute('app_wishlist');
    }
}
