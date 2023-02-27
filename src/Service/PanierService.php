<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\BoutiqueService;

// Service pour manipuler le panier et le stocker en session
class PanierService
{
    ////////////////////////////////////////////////////////////////////////////
    private $session;   // Le service session
    private $boutique;  // Le service boutique
    private $panier;    // Tableau associatif, la clé est un idProduit, la valeur associée est une quantité
                        //   donc $this->panier[$idProduit] = quantité du produit dont l'id = $idProduit
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session pour faire persister $this->panier

    // Constructeur du service
    public function __construct(RequestStack $requestStack, BoutiqueService $boutique)
    {
        // Récupération des services session et BoutiqueService
        $this->boutique = $boutique;
        $this->session = $requestStack->getSession();
        // Récupération du panier en session s'il existe, init. à vide sinon

        $this->panier = $this->session->get('panier');
        if ($this->panier == null) {
            $this->panier = array();
        }
    }

    // Renvoie le montant total du panier
    public function getTotal() : float
    {
        // init the total of the cart to 0
        $prixTotal = 0;
        // sum all the prices
        foreach ($this->panier as $id => $quantite) {
            $currentProduct = json_decode($this->boutique->findProduitById($id));
            $prixTotal += ($currentProduct->prix)*$quantite;
        }
        return $prixTotal;
    }

    // Renvoie le nombre de produits dans le panier
    public function getNombreProduits() : int
    {
        // initialisation du nombre de produit
        $nbProduct = 0;
        // parse the cart to add the quantity of each product
        foreach ($this->panier as $id => $quantity) {
            $nbProduct += $quantity;
        }
        return $nbProduct;
    }

    // Ajouter au panier le produit $idProduit en quantite $quantite 
    public function ajouterProduit(int $idProduit, int $quantite = 1) : void
    {
        if (isset($this->panier[$idProduit])) {
            $this->panier[$idProduit] += $quantite;
        } else {
            $this->panier[$idProduit] = $quantite;
        }
    }

    // Enlever du panier le produit $idProduit en quantite $quantite 
    public function enleverProduit(int $idProduit, int $quantite = 1) : void
    {
      unset($this->panier[$idProduit]);
    }

    // Supprimer le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) : void
    {
      /* A COMPLETER */
    }

    // Vider complètement le panier
    public function vider() : void
    {
      /* A COMPLETER */
    }

    // Renvoie le contenu du panier dans le but de l'afficher
    //   => un tableau d'éléments [ "produit" => un objet produit, "quantite" => sa quantite ]
    public function getContenu() : array
    {
        // initialisation de l'array
        $cart = array();
        foreach ($this->panier as $idProduct => $quantity) {
            $currentProduct = $this->boutique->findProduitById($idProduct);
            $cart = array("produit" => $currentProduct, "quantite" => $quantity);
        }
        return $cart;
    }

}