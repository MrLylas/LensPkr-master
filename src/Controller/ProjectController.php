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
    #[Route('/', name: 'project_feed')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findAll();

        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects
        ]);
    }

    #[Route('/project/new', name: 'new_project')]
    public function newProject(Request $request,EntityManagerInterface $entityManager): Response
    {
        $newProject = new Project();
        $form = $this->createForm(ProjectType::class, $newProject);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newProject->setCreatedAt(new \DateTimeImmutable());
            $newProject->setCreator($this->getUser());
            $entityManager->persist($newProject);
            $entityManager->flush();

            return $this->redirectToRoute('project_feed');
        }

        return $this->render('project/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/project/{id}', name: 'project')]
    public function project(Project $project): Response
    {
        return $this->render('project/project.html.twig', [
            'project' => $project
        ]);
    }

    #[Route('/project/{id}/edit', name: 'edit_project')]
    public function editProject(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($request->get('id'));
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_feed');
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
