<?php

namespace App\Controller;

use Dom\Entity;
use App\Entity\Image;
use App\Entity\Project;
use App\Form\ProjectType;
// use App\Entity\ProjectImage;
use App\Entity\ProjectImage;
use App\Service\FileUploader;
use App\Form\ProjectImageType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProjectController extends AbstractController
{
    #[Route('/project', name: 'project_feed')]
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
    public function editProject(Project $project,Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreatedAt(new \DateTimeImmutable());
            $project->setCreator($this->getUser());
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_feed');
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
 
    #[Route('/project/{id}/add-images', name: 'upload_project_images', methods: ['GET', 'POST'])]
    public function uploadImages(Project $project, Request $request, FileUploader $fileUploader, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProjectImageType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $images = $form->get('images')->getData(); // Récupère les fichiers
            $description = $form->get('description')->getData();
            

            foreach ($images as $imageFile) {
                // Upload de l'image
                $fileName = $fileUploader->upload($imageFile);
                
                
                // Création d'une entité Image
                $image = new Image();
                $image->setName($fileName);
                $image->setDescription($description->getDescription());
                
                $image->setCreatedAt(new \DateTimeImmutable());
                
                $em->persist($image);
                
                // Création d'une entrée ProjectImage pour lier l'image au projet
                $projectImage = new ProjectImage();
                $projectImage->setProject($project);
                $projectImage->setImage($image);
                
                $em->persist($projectImage);
            }
            
            $em->flush();
            
            $this->addFlash('success', 'Images ajoutées avec succès !');
            return $this->redirectToRoute('project', ['id' => $project->getId()]);
        }
        
        return $this->render('project/upload_images.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    #[Route('/project/{id}/delete_image', name: 'delete_image')]
    public function deleteImage(ProjectImage $projectImage, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($projectImage);
        $entityManager->flush();

        return $this->redirectToRoute('project', ['id' => $projectImage->getProject()->getId()]);
    }
}
 