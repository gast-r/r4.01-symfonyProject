<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BoutiqueService;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(BoutiqueService $boutique): Response
    {
        $categories = $boutique->findAllCategories();

        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,
            'nb_categories' => count($categories)
        ]);
    }

    #[Route('/rayon/{idCategorie}', name: 'boutique/app_boutique_rayon')]
    public function rayon(int $idCategorie) : Response
    {
        return $this->render('boutique/rayon.html.twig', [
            'controller_name' => 'BoutiqueController',
        ]);
    }


}
