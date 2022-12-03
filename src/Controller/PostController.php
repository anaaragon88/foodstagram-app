<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('post/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/post/{id}', name: 'VerPost')]
    public function VerPost($id, Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        return $this->render('post/verPost.html.twig', ['post' => $post]);
    }

    #[Route('/mi-perfil', name: 'MiPerfil')]
    public function MisPost(ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $user = $this->getUser();
        $posts = $em->getRepository(Post::class)->findBy(['user' => $user]);
        return $this->render('post/MisPosts.html.twig', ['posts' => $posts]);
    }
}
