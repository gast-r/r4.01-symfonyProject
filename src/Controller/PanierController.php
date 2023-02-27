<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{_locale}/panier',
    requirements: ['_locale' => '%app.supported_locales%']
    )]
class PanierController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'app_panier_index',
    )]
    public function index(PanierService $panier): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'cartContent' => $panier->getContenu(), // récupérer le contenu du panier
            'nbProduct' => $panier->getNombreProduits(), // le nombre total de produits
            'totalPrice' => $panier->getTotal(), // le prix total de ces produits
        ]);
    }
    #[Route(
        path: '/ajouter/{idProduct}/{quantity}',
        name: 'app_panier_ajouter',
    )]
    public function ajouter(PanierService $panier, $idProduct, $quantity): Response
    {
        $panier->ajouterProduit($idProduct, $quantity);

        // appeler direct la méthods au app_panier_index
        return $this->redirectToRoute('app_panier_index');
    }



}
