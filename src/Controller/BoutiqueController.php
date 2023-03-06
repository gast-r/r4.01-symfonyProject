<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BoutiqueService;

class BoutiqueController extends AbstractController
{
    #[Route(
        path: '/{_locale}/boutique',
        name: 'app_boutique',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function index(BoutiqueService $boutique): Response
    {
        $categories = $boutique->findAllCategories();

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
    public function rayon(BoutiqueService $boutique, int $idCategorie) : Response
    {
        $produits = $boutique->findProduitsByCategorie($idCategorie);
        $category = $boutique->findCategorieById($idCategorie);

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
    public function chercher(BoutiqueService $boutique, string $recherche="") : Response
    {
        $findedProducts = $boutique->findProduitsByLibelleOrTexte(urldecode($recherche));

        return $this->render('boutique/chercher.html.twig', [
            'controller_name' => 'BoutiqueController',
            'products' => $findedProducts,
            'recherche' => $recherche,
        ]);
    }


}
