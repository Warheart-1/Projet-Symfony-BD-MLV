<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;
use DateTimeImmutable;

#[Route('/article')]
class ArticleController extends AbstractController
{
    public function __construct(protected ManagerRegistry $registry)
    {
    }
    #[Route('/', name: 'app_article')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $articles = $user->getArticles()->getValues();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    #[Route('/show/{id}', name: 'app_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article
        ]);
    }

    #[Route('/create', name: 'app_article_create')]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new DateTimeImmutable());
            $article->setCreatedBy($this->getUser());
            $this->registry->getManager()->persist($article);
            $this->registry->getManager()->flush();
            $this->addFlash('success', 'Article créé avec succès');
            return $this->redirectToRoute('app_article');
        }
        return $this->render('article/create.html.twig', [
            'controller_name' => 'ArticleController',
            'formArticle' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'app_article_edit')]
    public function edit(Request $request, $id, ArticleRepository $articleRepository): Response
    {

        $articleRegister = $this->registry->getRepository(Article::class)->find($id);

        $formArticle = $this->createForm(ArticleType::class, $articleRegister);
        $formArticle->handleRequest($request);
        
        if($formArticle->isSubmitted() && $formArticle->isValid()){
            $articleRegister = $formArticle->getData();

            $articleRegister->setModifiedAt(new DateTimeImmutable());

            $article = $this->registry->getManager();
            $article->persist($articleRegister);
            $article->flush();

            return $this->redirectToRoute('app_article');
        }

        return $this->render('article/edit.html.twig', [
            'controller_name' => 'ArticleController',
            'articleRegister' => $articleRegister,
            'formArticle' => $formArticle->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'app_article_delete')]
    public function delete($id): Response
    {
        $articleRegister = $this->registry->getRepository(Article::class)->find($id);

        $article = $this->registry->getManager();
        $article->remove($articleRegister);
        $article->flush();

        return $this->redirectToRoute('app_article');
    }
}
