<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class PostController extends AbstractController
{
    #[Route('/crear-post', name: 'crear_post')]
    public function index(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('picture')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Tu imagen no se subió correctamente');
                }

                $post->setPicture($newFilename);
            }
            $user = $this->getUser();
            $post->setUser($user);
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', Post::SUCCESS_POST);
            return $this->redirectToRoute('crear_post');
        }
        return $this->render('post/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/post/{id}', name: 'VerPost')]
    public function VerPost($id, Request $request, ManagerRegistry $doctrine, PaginatorInterface $paginator)
    {
        $em = $doctrine->getManager();
        $comment = new Comments();
        $post = $em->getRepository(Post::class)->find($id);
        $queryComments = $em->getRepository(Comments::class)->BuscarComentariosDeUNPost($post->getId());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $comment->setPost($post);
            $comment->setUser($user);
            $em->persist($comment);
            $em->flush();
            $this->addFlash('Success', Comments::COMENTARIO_AGREGADO_EXITOSAMENTE);
            return $this->redirectToRoute('VerPost', ['id' => $post->getId()]);
        }
        $pagination = $paginator->paginate(
            $queryComments, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

        return $this->render('post/verPost.html.twig', ['post' => $post, 'form' => $form->createView(), 'comments' => $pagination]);
    }

    #[Route('/mi-perfil', name: 'MiPerfil')]
    public function MisPost(ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $user = $this->getUser();
        $posts = $em->getRepository(Post::class)->findBy(['user' => $user]);
        return $this->render('post/MisPosts.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/Likes", options={"expose"=true}, name="Likes")
     */

    #[Route('/likes', name: 'Likes')]
    public function Like(Request $request, ManagerRegistry $doctrine)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $doctrine->getManager();
            $user = $this->getUser();
            $id = $request->request->get('id');
            $post = $em->getRepository(Post::class)->find($id);
            $likes = $post->getLikes();
            $likes .= $user->getName_user() . ', ';
            $post->setLikes($likes);
            $em->flush();
            return new JsonResponse(['likes' => $likes]);
        } else {
            throw new \Exception('Estás tratando de hackearme?');
        }
    }
}
