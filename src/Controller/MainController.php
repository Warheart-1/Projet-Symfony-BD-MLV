<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;

#[Route('/')]
class MainController extends AbstractController
{
    public function __construct(protected ManagerRegistry $resgistry, protected UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/accueil', name: 'app_main')]
    public function index(): Response
    {
        $postRegisters = $this->resgistry->getRepository(Article::class)->findByDateCreatedLimit3(3);
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'postRegisters' => $postRegisters
        ]);
    }
    #[Route('/accueil/{id}', name: 'app_main_show')]
    public function show(Article $article): Response
    {
        
        return $this->render('main/show.html.twig', [
            'controller_name' => 'MainController',
            'article' => $article
        ]);
    }
}
