<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxAdminController extends AbstractController
{
    #[Route('/admin/article/category/{category}', name: 'app_ajax_admin_category')]
    public function index(String $category): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            $article = $this->getDoctrine()->getRepository(Article::class)->findBy(['Category' => $category]);
            $articles = [];
            $idx = 0;
            foreach ($article as $a) {
                $temps = array(
                    'name' => $a->getTitle(),
                    'description' => $a->getDescription(),
                    'content' => $a->getContent(),
                    'quantity' => $a->getQuantity(),
                    'category' => $a->getCategory(),
                    'price' => $a->getPrice(),
                    'date' => $a->getCreatedAt()->format('d/m/Y H:i:s'),
                    'dateupdate' => $a->getModifiedAt() ==! null ? $a->getModifiedAt()->format('d/m/Y H:i:s') : null,
                    'author' => $a->getCreatedBy() ==! null ? $a->getCreatedBy()->getUsername() : null,
                    'id' => $a->getId(),
                );
                $articles[$idx++] = $temps;
            }
            return new JsonResponse($articles);
        }
        else {
            return $this->redirectToRoute('app_main');
        }
    }
}
