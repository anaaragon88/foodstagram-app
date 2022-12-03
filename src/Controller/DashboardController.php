<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(PaginatorInterface $paginator, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        if ($user) {
            $em = $doctrine->getManager();
            $query = $em->getRepository(Post::class)->FindAllPosts();
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                5 /*limit per page*/
            );
            return $this->render('dashboard/index.html.twig', [
                'pagination' => $pagination
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
