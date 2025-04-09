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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//Final class signifie que la classe ne peut avoir d'enfants 
final class JobController extends AbstractController
{
    #[Route('/job/{id}', name: 'app_jobs')]
    public function index(JobRepository $repository,Request $request,EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        $jobs = $repository->recentsJobs();
        $asks = $entityManager->getRepository(Ask::class)->findAll();

        //On effectue un ternaire nous permettant de récupérer l'utilisateur en session si il est connecté 
        //$user correspond au user en session, il est aussi notre condition
        // ? correspond au signe de question qui permet de faire un ternaire
        //Elle se compose comme cela : condition ? si_vrai : si_faux
        $appliedAsks = $user ? $entityManager->getRepository(Ask::class)->findBy(['user' => $user]) : [];

        //  on transforme `appliedAsks` en un tableau contenant uniquement les IDs des jobs déjà postulés
        $appliedJobIds = array_map(fn($ask) => $ask->getJob()->getId(), $appliedAsks);

        $jobs = $paginator->paginate(
            $jobs, 
            $request->query->getInt('page', 1),
             6
            )
        ;
        return $this->render('job/index.html.twig', [
            'meta_description' => 'Find your job here',
            'user' => $user,
            // 'user_id' => $user->getId(),
            'jobs' => $jobs,
            'asks' => $asks,
            'appliedAsks' => $appliedJobIds,
        ]);
    }
    // Création de l'annonce
    #[Route('/job/post/{id}', name: 'post_job')]
    public function post_job(Request $request,User $user, EntityManagerInterface $entityManager): Response
    {
        //Création d'un objet Job pour la nouvelle annonce
        $post = new Job();
        // Création du formulaire pour l'annonce
        $form = $this->createForm(PostJobType::class, $post);
        // Traitement de la requête et soumission du formulaire
        $form->handleRequest($request);
        // Ajout de l'utilisateur connecté
        $user = $this->getUser();
        $post->setUser($user);
        // Ajout de la date
        $post->setCreation(new \DateTime());
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'annonce
            $entityManager->persist($post);
            $entityManager->flush();
            // Redirection vers la page des annonces
            return $this->redirectToRoute('app_jobs', ['id' => $user->getId()]);
        }
        // Affichage du formulaire pour la création de l'annonce
        return $this->render('job/post.html.twig', [
            'controller_name' => 'JobController',
            'user' => $user,
            'id' => $user->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/job/detail/{job_name}/', name: 'app_job_detail')]
    public function job_detail(Job $job ,EntityManagerInterface $entityManager, string $job_name): Response
    {
        // Récupérer l'utilisateur en session
        $user = $this->getUser();
        $job = $entityManager->getRepository(Job::class)->findOneBy(['job_name' => $job_name]);
        $appliedAsks = $user ? $entityManager->getRepository(Ask::class)->findBy(['user' => $user]) : [];

        return $this->render('job/detail.html.twig',
        [
            'meta_description' => 'More details about the job',
            'user' => $user,
            'user_id' => $user->getId(),
            'job' => $job,
            'appliedAsks' => $appliedAsks
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
    // dd($appliedAsks);
    
    // Ajouter l'utilisateur au job
    $user->addAsk($ask);
    $entityManager->persist($ask);
    $entityManager->flush();

    // Ajouter un message flash de confirmation et rediriger vers la page des offres
    $this->addFlash('success', 'Votre candidature a été enregistrée.');
    return $this->redirectToRoute('app_jobs',
        [
            'meta_description' => 'Apply to the job',
            'id' => $job->getId(),
            'appliedAsks' => $appliedAsks
        ]);
    }
}