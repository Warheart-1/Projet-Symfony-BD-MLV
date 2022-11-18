<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class MainController extends AbstractController
{
    public function __construct(protected ManagerRegistry $resgistry, protected UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        if ($this->getUser()) {
            $postRegisters = $this->resgistry->getRepository(Article::class)->findAll();
        }
        else {
            $postRegisters = $this->resgistry->getRepository(Article::class)->findByDateCreatedLimit3(3);
        }
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'articles' => $postRegisters
        ]);
    }
    
    #[Route('/accueil/{id}', name: 'app_main_show')]
    public function show(Request $request, $id, ArticleRepository $articleRepository): Response
    {
        
        return $this->render('main/show.html.twig', [
            'controller_name' => 'MainController',
            'article' => $article
        ]);
    }
}
