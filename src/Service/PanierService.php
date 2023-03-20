<?php
namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Entity\Usager;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;

// Service pour manipuler le panier et le stocker en session
class PanierService
{
    ////////////////////////////////////////////////////////////////////////////
    private $session;   // Le service session
    private $doctrine;  // Le service doctrine
    private $panier;    // Tableau associatif, la clé est un idProduit, la valeur associée est une quantité
                        //   donc $this->panier[$idProduit] = quantité du produit dont l'id = $idProduit
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session pour faire persister $this->panier

    // Constructeur du service
    public function __construct(RequestStack $requestStack, ManagerRegistry $doctrine)
    {
        // Récupération des services session et ManagerRepository
        $this->doctrine = $doctrine;
        $this->session = $requestStack->getSession();
        // Récupération du panier en session s'il existe, init. à vide sinon
        $this->panier = $this->session->get('panier', array());
    }

    // Renvoie le montant total du panier
    public function getTotal() : float
    {
        // init the total of the cart to 0
        $prixTotal = 0;
        // for each product in the cart, add the price*quantity of the product to the result
        foreach ($this->panier as $id => $quantite) {
            $currentProduct = $this->doctrine
            ->getManager()
            ->getRepository(Produit::class)
            ->find($id);

            $prixTotal += $currentProduct->getPrix()*$quantite;
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
        $produit = $this->doctrine
            ->getManager()
            ->getRepository(Produit::class)
            ->find($idProduit);
        if(!$produit) {
            return;
        }

        if (isset($this->panier[$idProduit])) {
            $this->panier[$idProduit] += $quantite;
        } else {
            $this->panier[$idProduit] = $quantite;
        }
        $this->session->set('panier', $this->panier);
    }

    // Enlever du panier le produit $idProduit en quantite $quantite 
    public function enleverProduit(int $idProduit, int $quantite = 1) : void
    {
      // si la quantite du produit est supérieur à quantite (la quantite à enlever)
        // on enlève cette quantite, sinon on supprime le produit
      if ($this->panier[$idProduit] > $quantite) {
          $this->panier[$idProduit] -= $quantite;
      } else {
          unset($this->panier['idProduit']);
      }
      $this->session->set('panier', $this->panier);
    }

    // Supprimer le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) : void
    {
      unset($this->panier[$idProduit]);
      $this->session->set('panier', $this->panier);
    }

    // Vider complètement le panier
    public function vider() : void
    {
      $this->session->remove('panier');
    }

    // Renvoie le contenu du panier dans le but de l'afficher
    //   => un tableau d'éléments [ "produit" => un objet produit, "quantite" => sa quantite ]
    public function getContenu() : array
    {
        // initialisation de l'array
        $cart = array();
        foreach ($this->panier as $idProduct => $quantity) {
            $currentProduct = $this->doctrine
            ->getManager()
            ->getRepository(Produit::class)
            ->find($idProduct);
            array_push($cart, array("produit" => $currentProduct, "quantite" => $quantity));
        }
        return $cart;
    }

    /**
     * Create an order with the info of the cart PanierService for the user {@link Usager}usager.
     * @param Usager $usager - the user that make the order
     * @return Commande|null - the order create with the info of the cart {@link PanierService}PanierService
     */
    public function panierToCommande(Usager $usager) : ?Commande {
        $order = new Commande();
        // create of the order
        $order->getDateCreation(new \DateTime());

        // for each product in the cart,
        //  - create a LigneCommande for this order
        foreach ($this->panier as $idProduct => $quantity) {
            // get the product with his id
            $currentProduct = $this->doctrine
                ->getManager()
                ->getRepository(Produit::class)
                ->find($idProduct);
            $currentPrice = $currentProduct->getPrix()*$quantity;
            // create the LigneCommande with these information
            //  - the Produit of the LigneCommande
            //  - the quantity of the Produit
            //  - the total price for this product
            $ligneCommande = new LigneCommande();
            $ligneCommande->setArticle($currentProduct);
            $ligneCommande->setQuantite($quantity);
            $ligneCommande->setPrix($currentPrice);

            // add the line to the order
            $order->addLignesCommande($ligneCommande);
        }

        // add the order to the user
        $usager->addCommande($order);

        // empty the cart
        $this->vider();

        return $order;
    }

}