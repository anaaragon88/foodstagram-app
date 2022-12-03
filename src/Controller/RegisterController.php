<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegisterController extends AbstractController
{
    #[Route('/registro', name: 'app_register')]
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $user->setPassword($passwordHasher->hashPassword($user, $form['password']->getData()));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', User::REGISTER_SUCCESS);
            return $this->redirectToRoute('app_register');
        }
        return $this->render('register/index.html.twig', [
            'formulario' => $form->createView()
        ]);
    }
}
