<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/')]
class ArticleController extends AbstractController
{
    public function __construct(protected ManagerRegistry $registry)
    {
    }
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_show')]
    public function show($id): Response
    {
        $postRegister = $this->registry->getRepository(Article::class)->find($id);
        return $this->render('article/show.html.twig', [
            'controller_name' => 'ArticleController',
            'postRegister' => $postRegister
        ]);
    }
}
