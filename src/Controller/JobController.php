<?php

namespace App\Controller;

use App\Entity\Ask;
use App\Entity\Job;
use App\Entity\User;
use App\Form\AskType;
use App\Form\PostJobType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\AskRepository;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class JobController extends AbstractController
{
    #[Route('/job/{id}', name: 'app_job')]
    public function index(User $user,Request $request, JobRepository $repository, EntityManagerInterface $entityManager): Response
    {
        //Version findAll() :
        // EntityManagerInterface $entityManager
        // $jobs = $entityManager->getRepository(Job::class)->findAll();
        
        //Tentative de pagination: 
        
        // Récupérer la page actuelle depuis la requête. Si aucune page n'est fournie, la page par défaut est 1
        // $page = $request->query->getInt('page', 1);
        $jobs = $repository->findAll();
        $job = $entityManager->getRepository(Job::class)->findOneBy(['user' => $user->getId()]);
        // dd($job);
        // $ask = $entityManager->getRepository(Ask::class)->findBy(['job' => $job->getId()]);


        //Définition du nombre de pages, ceil() arrondit au nombre entier le plus proche
        // $maxPage = ceil($jobs->count() / 2);


        return $this->render('job/index.html.twig', [
            'controller_name' => 'JobController',
            'user' => $user,
            'id' => $user->getId(),
            'jobs' => $jobs,
            'ask' => $ask
            // 'maxPage' => $maxPage,
            // 'page' => $page
        ]);
    }

    #[Route('/job/post/{id}', name: 'post_job')]
    public function post_job(Request $request,User $user, EntityManagerInterface $entityManager): Response
    {
        $post = new Job();
        $form = $this->createForm(PostJobType::class, $post);
        $form->handleRequest($request);
        $user = $this->getUser();
        $post->setUser($user);
        $post->setCreation(new \DateTime());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_job', ['id' => $user->getId()]);
        }

        return $this->render('job/post.html.twig', [
            'controller_name' => 'JobController',
            'user' => $user,
            'id' => $user->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/job/detail/{id}/', name: 'app_job_detail')]
    public function job_detail(Job $job, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // $asks = $job->getAsks();
        // dd($ask);

        return $this->render('job/detail.html.twig',
        [
            'controller_name' => 'JobController',
            'user' => $user,
            'user_id' => $user->getId(),
            'job' => $job,
        ]
    );
    }
    #[Route('/job/apply/{id}/', name: 'app_apply_job')]
        public function apply_job(Job $job, Request $request, EntityManagerInterface $entityManager): Response
        {
            $user = $this->getUser();
            $job = $entityManager->getRepository(Job::class)->findOneBy(['id' => $job->getId()]);
            $ask = new Ask();
            $form = $this->createForm(AskType::class, $ask);
            $form->handleRequest($request);
            $ask->setUser($user);
            $ask->setJob($job);
            $ask->setDateAsk(new \DateTime());

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($ask);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_job', ['id' => $user->getId()]);
            }

            return $this->render('job/apply.html.twig',
            [
                'controller_name' => 'JobController',
                'form' => $form,
                'user' => $user,
                'user_id' => $user->getId(),
                'job' => $job,
            ]);
        }
}
