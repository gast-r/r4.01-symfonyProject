<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class BoutiqueController extends AbstractController
{
    #[Route(
        path: '/{_locale}/boutique',
        name: 'app_boutique',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getManager()
                                ->getRepository(Categorie::class)
                                ->findAll();

        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,
        ]);
    }

    #[Route(
        path: '/{_locale}/boutique/rayon/{idCategorie}',
        name: 'app_boutique_rayon',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function rayon(ManagerRegistry $doctrine, int $idCategorie) : Response
    {
        $produits = $doctrine
            ->getManager()
            ->getRepository(Produit::class)
            ->findBy(['categorie' => $idCategorie]);

        $category = $doctrine
            ->getManager()
            ->getRepository(Categorie::class)
            ->find($idCategorie);

        return $this->render('boutique/rayon.html.twig', [
            'controller_name' => 'BoutiqueController',
            'products' => $produits,
            'category' => $category,
        ]);
    }

    #[Route(
        path: '/{_locale}/boutique/chercher/{recherche}',
        name: 'app_boutique_recherche',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function chercher(ManagerRegistry $doctrine, string $recherche="") : Response
    {
        $findedProducts = $doctrine
            ->getManager()
            ->getRepository(Produit::class)
            ->findByLibelleOrTexte(urldecode($recherche));

        return $this->render('boutique/chercher.html.twig', [
            'controller_name' => 'BoutiqueController',
            'products' => $findedProducts,
            'recherche' => $recherche,
        ]);
    }


}
