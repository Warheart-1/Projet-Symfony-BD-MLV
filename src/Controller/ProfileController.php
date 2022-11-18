<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    public function __construct(protected ManagerRegistry $registry, protected UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user
        ]);
    }

    #[Route('/edit/{id}', name: 'app_profile_edit')]
    public function edit(Request $request, $id ,UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if($id != $this->getUser()->getId()){
            $this->addFlash('danger', 'Vous ne pouvez pas modifier ce profil');
            return $this->redirectToRoute('app_profile');
        }

        $userRegister = $this->registry->getRepository(User::class)->find($id);

        $formUser = $this->createForm(UserType::class, $userRegister);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $userRegister->setPassword($this->passwordHasher->hashPassword($userRegister, $userRegister->getPassword()));
            $this->registry->getManager()->persist($userRegister);
            $this->registry->getManager()->flush();
            $this->addFlash('success', 'Profil modifié avec succès');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'controller_name' => 'ProfileController',
            'formUser' => $formUser->createView(),
            'user' => $userRegister
        ]);
    }

}
