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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class JobController extends AbstractController
{
    #[Route('/job/{id}', name: 'app_jobs')]
    public function index(JobRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        //Version findAll() :
        // EntityManagerInterface $entityManager
        // $jobs = $entityManager->getRepository(Job::class)->findAll();
        
        //Tentative de pagination: 
        
        // Récupérer la page actuelle depuis la requête. Si aucune page n'est fournie, la page par défaut est 1
        // $page = $request->query->getInt('page', 1);

        $jobs = $repository->findAll();
        $asks = $entityManager->getRepository(Ask::class)->findAll();

        $appliedAsks = $user ? $user->getAsks() : [];

        // dd($appliedAsks);
        // dd($user);
        

        //Définition du nombre de pages, ceil() arrondit au nombre entier le plus proche
        // $maxPage = ceil($jobs->count() / 2);


        return $this->render('job/index.html.twig', [
            'controller_name' => 'JobController',
            'user' => $user,
            'user_id' => $user->getId(),
            'jobs' => $jobs,
            'asks' => $asks,
            'appliedAsks' => $appliedAsks,
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

            return $this->redirectToRoute('app_jobs', ['id' => $user->getId()]);
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

    #[Route('/job/apply/{id}', name: 'apply_to_job')]
public function apply(Job $job, EntityManagerInterface $entityManager, Security $security): Response
{
    // Récupérer l'utilisateur en session
    $user = $security->getUser();
    $appliedAsks = $user ? $user->getAsks() : [];
    $ask = new Ask();

    // Vérifier si l'utilisateur est connecté
    //si il n'est pas connecté, rediriger vers la page de login
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }
    
    // Vérifier si l'utilisateur a déjà postulé
    if ($user->getJobs()->contains($job)) {
        // ajout d'un message flash en cas de postulation multiple
        $this->addFlash('warning', 'Vous avez déjà postulé à cette offre.');
        // Rediriger vers la page des offres
        return $this->redirectToRoute('app_jobs',['id' => $user->getId()]);
    }

    // Ajouter l'utilisateur au job
    $ask->setDateAsk(new \DateTime());
    $ask->setJob($job);
    dd($appliedAsks); 
    
    // Ajouter l'utilisateur au job
    $user->addAsk($ask);
    $entityManager->persist($ask);
    $entityManager->flush();

    // Ajouter un message flash de confirmation et rediriger vers la page des offres
    $this->addFlash('success', 'Votre candidature a été enregistrée.');
    return $this->redirectToRoute('app_jobs',
        [
            'id' => $job->getId(),
            'appliedAsks' => $appliedAsks
        ]);
    }
}