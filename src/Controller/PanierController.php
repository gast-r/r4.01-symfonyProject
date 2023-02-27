<?php

namespace App\Controller;

use App\Service\BoutiqueService;
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
    public function ajouter(PanierService $panier, BoutiqueService $boutique, $idProduct, $quantity): Response
    {
        if ($boutique->findProduitById($idProduct) != null) {
            $panier->ajouterProduit($idProduct, $quantity);
        } else {
            throw $this->createNotFoundException("Le produit n\'existe pas !");
        }
        // appeler direct la méthods au app_panier_index
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route(
        path: '/enlever/{idProduct}/{quantity}',
        name: 'app_panier_enlever',
    )]
    public function enlever(PanierService $panier, $idProduct, $quantity): Response
    {
        $panier->enleverProduit($idProduct, $quantity);

        // appeler direct la méthods au app_panier_index
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route(
        path: '/supprimer/{idProduct}',
        name: 'app_panier_supprimer',
    )]
    public function supprimer(PanierService $panier, $idProduct): Response
    {
        $panier->supprimerProduit($idProduct);

        // appeler direct la méthods au app_panier_index
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route(
        path: '/vider',
        name: 'app_panier_vider',
    )]
    public function vider(PanierService $panier): Response
    {
        $panier->vider();

        // appeler direct la méthods au app_panier_index
        return $this->redirectToRoute('app_panier_index');
    }



}
