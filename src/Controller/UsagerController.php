<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/usager',
    requirements: ['_locale' => '%app.supported_locales%']
)]
class UsagerController extends AbstractController
{
    #[Route(
        path:'/',
        name: 'app_usager_index',
        methods: ['GET']
    )]
    public function index(UsagerRepository $usagerRepository): Response
    {
        return $this->render('usager/index.html.twig', [
            // sent to the template the usager with id = 1
            'usager' => $usagerRepository->find(1),
        ]);
    }

    #[Route(
        path: '/new',
        name: 'app_usager_new',
        methods: ['GET', 'POST']
    )]
    public function new(Request $request, UsagerRepository $usagerRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the password
            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());
            $usager->setPassword($hashedPassword);

            // define the role of the usager
            $usager->setRoles(["ROLE_CLIENT"]);


            $usagerRepository->save($usager, true);

            return $this->redirectToRoute('app_usager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form,
        ]);
    }
}
