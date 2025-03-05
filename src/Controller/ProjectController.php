<?php

namespace App\Controller;

use Dom\Entity;
use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProjectController extends AbstractController
{
    #[Route('/project', name: 'project_feed')]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }

    #[Route('/project/new', name: 'new_project')]
    public function newProject(Request $request,EntityManagerInterface $entityManager): Response
    {
        $newProject = new Project();
        $form = $this->createForm(ProjectType::class, $newProject);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $form->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($newProject);
            $entityManager->flush();
        }

        return $this->render('project/new.html.twig', [
            'form' => $form
        ]);
    }
}
