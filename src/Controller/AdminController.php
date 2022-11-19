<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    public function __construct(protected ManagerRegistry $registry, protected UserPasswordHasherInterface $passwordHasher)
    {
    }
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            return $this->render('admin/index.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/article', name: 'app_admin_article')]
    public function article(): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $article = $this->getDoctrine()->getRepository(Article::class)->findAll();
            $categories = [];
            foreach($article as $a) {
                if (!in_array($a->getCategory(),$categories)) {
                    $categories[] = $a->getCategory();
                }
            }
            return $this->render('admin/article.html.twig', [
                'controller_name' => 'AdminController',
                'articles' => $article,
                'categories' => $categories
            ]);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/article/edit/{id}', name: 'app_admin_article_edit')]
    public function articleShow(Request $request, $id, ArticleRepository $articleRepository): Response
    {

        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $articleRegister = $articleRepository->find($id);

            $form = $this->createForm(ArticleType::class, $articleRegister);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $articleRegister = $form->getData();
                $articleRegister->setModifiedAt(new DateTimeImmutable());

                $this->registry->getManager()->persist($articleRegister);
                $this->registry->getManager()->flush();
                $this->addFlash('success', 'Article modifié avec succès');
                return $this->redirectToRoute('app_admin_article');
            }
            return $this->render('admin/article_edit.html.twig', [
                'controller_name' => 'AdminController',
                'article' => $articleRegister,
                'formArticle' => $form->createView()
            ]);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/article/create', name: 'app_admin_article_create')]
    public function articleCreate(Request $request): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $article = new Article();
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $article->setCreatedAt(new DateTimeImmutable());
                $article->setCreatedBy($this->getUser());
                $this->registry->getManager()->persist($article);
                $this->registry->getManager()->flush();
                $this->addFlash('success', 'Article créé avec succès');
                return $this->redirectToRoute('app_admin_article');
            }
            return $this->render('admin/article_create.html.twig', [
                'controller_name' => 'AdminController',
                'formArticle' => $form->createView()
            ]);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/article/delete/{id}', name: 'app_admin_article_delete')]
    public function articleDelete($id, ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $article = $articleRepository->find($id);
            $this->registry->getManager()->remove($article);
            $this->registry->getManager()->flush();
            $this->addFlash('success', 'Article supprimé avec succès');
            return $this->redirectToRoute('app_admin_article');
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/profile', name: 'app_admin_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $profile = $this->getDoctrine()->getRepository(User::class)->findAll();
            return $this->render('admin/profile.html.twig', [
                'controller_name' => 'AdminController',
                'profiles' => $profile
            ]);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/profile/edit/{id}', name: 'app_admin_profile_edit')]
    public function profileEdit(Request $request, $id, UserRepository $articleRepository): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $profileRegister = $articleRepository->find($id);

            $formProfile = $this->createForm(AdminUserType::class, $profileRegister);
            $formProfile->handleRequest($request);

            if ($formProfile->isSubmitted() && $formProfile->isValid()) {
                $profileRegister = $formProfile->getData();

                $profileRegister->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

                $entityManager = $this->registry->getManager();
                $entityManager->persist($profileRegister);
                $entityManager->flush();

                $this->addFlash('success', 'Profil modifié avec succès');
                return $this->redirectToRoute('app_admin_profile');
            }
            return $this->render('admin/profile_edit.html.twig', [
                'controller_name' => 'AdminController',
                'profile' => $profileRegister,
                'formUser' => $formProfile->createView()
            ]);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/profile/create', name: 'app_admin_profile_create')]
    public function profileCreate(Request $request): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $profile = new User();
            $formProfile = $this->createForm(AdminUserType::class, $profile);
            $formProfile->handleRequest($request);

            if ($formProfile->isSubmitted() && $formProfile->isValid()) {
                $profile = $formProfile->getData();
                
                $profile->setPassword($this->passwordHasher->hashPassword($profile, $profile->getPassword()));
                $entityManager = $this->registry->getManager();
                $entityManager->persist($profile);
                $entityManager->flush();

                $this->addFlash('success', 'Profil créé avec succès');
                return $this->redirectToRoute('app_admin_profile');
            }
            return $this->render('admin/profile_create.html.twig', [
                'controller_name' => 'AdminController',
                'formUser' => $formProfile->createView()
            ]);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/admin/profile/delete/{id}', name: 'app_admin_profile_delete')]
    public function profileDelete($id, UserRepository $articleRepository): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $profile = $articleRepository->find($id);
            $article = $this->getDoctrine()->getRepository(Article::class)->findArticleByAuthor($profile);
            foreach ($article as $a) {
                $a->setCreatedBy(null);
            }
            $this->registry->getManager()->remove($profile);
            $this->registry->getManager()->flush();
            $this->addFlash('success', 'Profil supprimé avec succès');
            return $this->redirectToRoute('app_admin_profile');
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }
}
