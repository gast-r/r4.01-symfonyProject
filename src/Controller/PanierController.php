<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route(
        path: '/{_locale}/panier',
        name: 'app_panier_index',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function index(PanierService $panier): Response
    {
        // on récupère le contenu du panier
        $contenu_panier = $panier->getContenu();
        // le nombre de produit
        $nbProduct = $panier->getNombreProduits();
        // and the total price of the cart
        $totalPrice = $panier->getTotal();
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'cartContent' => $contenu_panier,
            'nbProduct' => $nbProduct,
            'totalPrice' => $totalPrice,
        ]);
    }
}
