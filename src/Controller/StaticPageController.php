<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StaticPageController extends AbstractController
{
    #[Route('/privacy-policy', name: 'app_privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('static/privacy-policy.html.twig');
    }

    #[Route('/terms-and-conditions', name: 'app_terms_conditions')]
    public function termsAndConditions(): Response
    {
        return $this->render('static/terms-conditions.html.twig');
    }

    #[Route('/refund-policy', name: 'app_refund_policy')]
    public function refundPolicy(): Response
    {
        return $this->render('static/refund-policy.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('static/contact.html.twig');
    }

    #[Route('/test-cart', name: 'app_test_cart')]
    public function testCart(): Response
    {
        return $this->render('test_cart.html.twig');
    }
}
