<?php

namespace App\Controller;

use Dom\Entity;
use App\Entity\Team;
use App\Entity\User;
use App\Form\NewTeamType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\TextUI\XmlConfiguration\File;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TeamController extends AbstractController
{
    #[Route('/team', name: 'recent_team')]
    public function index(TeamRepository $teamRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $teams = $teamRepository->recentsTeams();

        $teams = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('team/index.html.twig', [
            'teams' => $teams
        ]);
    }
    #[Route('/team/MyTeams', name: 'my_teams')]
    public function myTeams(TeamRepository $teamRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $user_id = $this->getUser()->getId();
        $teams = $teamRepository->myTeams($user_id);

        $teams = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('team/index.html.twig', [
            'teams' => $teams
        ]);
    }

    #[Route('/team/PopularTeams', name: 'popular_teams')]
    public function popularTeams(TeamRepository $teamRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $teams = $teamRepository->popularTeams();
        
        $teams = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('team/index.html.twig', [
            'teams' => $teams
        ]);
    }

    #[Route('/team/FollowedTeams', name: 'followed_teams')]
    public function followedTeams(TeamRepository $teamRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user_id = $this->getUser()->getId();
        $teams = $teamRepository->followedTeams($user_id);

        $teams = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('team/index.html.twig', [
            'teams' => $teams
        ]);
    }

    #[Route('/team/new', name: 'new_team')]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Construction du formulaire
        $newTeam = new Team();
        // Envoyer le formulaire
        $form = $this->createForm(NewTeamType::class, $newTeam);
        // Traiter le formulaire
        $form->handleRequest($request);

        // Si le formulaire est envoyé
        if($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier
            $teamPic = $form->get('team_pic')->getData();
            $bannerPic = $form->get('team_banner')->getData();
            // Enregistrer le fichier
            if ($teamPic) {
                $fileName = $fileUploader->upload($teamPic);
                $newTeam->setTeamPic($fileName);
            }
            if ($bannerPic) {
                $fileName = $fileUploader->upload($bannerPic);
                $newTeam->setTeamBanner($fileName);
            }
            // Enregistrer l'equipe
            $newTeam = $form->getData();
            // Declarer la date
            $newTeam->setCreatedAt(new \DateTimeImmutable());
            // Declarer le createur
            $newTeam->setCreator($this->getUser());
            // Ajouter le createur a l'equipe
            $newTeam->addMembership($this->getUser());
            // Enregistrer
            $entityManager->persist($newTeam);
            $entityManager->flush();
            // Redirection vers le feed d'equipe
            return $this->redirectToRoute('recent_team');
        }
        // Afficher le formulaire
        return $this->render('team/new.html.twig', [
            'controller_name' => 'TeamController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/team/{id}', name: 'team_show')]
    public function show(ProjectRepository $projectRepository,EntityManagerInterface $entityManager, int $id): Response
    {
        // je recupere l'equipe
        $team = $entityManager->getRepository(Team::class)->find($id);
        // je recupere ses followers dans mon entité team
        $followers = $team->getFollow();
        // je recupere ses projets
        $projects = $projectRepository->teamProjects($id);

        return $this->render('team/show.html.twig', [
            'controller_name' => 'TeamController',
            'team' => $team,
            'followers' => $followers,
            'projects' => $projects,
        ]);
    }

    #[Route('/team/{id}/follow', name: 'team_follow')]
    public function follow(Team $team, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        // Vérifier si l'utilisateur suit déjà l'équipe
        if ($user->getFollowedTeams()->contains($team)) {
            // Si l'utilisateur suit déjà, on désabonne (unfollow)
            $user->removeFollowedTeam($team);
            $this->addFlash('success', 'Vous avez arrêté de suivre cette équipe.');
        } else {
            // Sinon, on ajoute l'équipe à la liste des suivis
            $user->addFollowedTeam($team);
            $this->addFlash('success', 'Vous suivez désormais cette équipe.');
        }

        // Sauvegarder les changements en base de données
        $entityManager->flush();

        return $this->redirectToRoute('team_show', ['id' => $team->getId()]);
    }

    #[Route('/team/add-member/{userId}', name: 'team_add_member', methods: ['POST'])]
    public function addMember(int $userId,Request $request,EntityManagerInterface $entityManager,TeamRepository $teamRepository,UserRepository $userRepository): Response {
       
    $currentUser = $this->getUser();

    if (!$currentUser instanceof User) {
        // Si l'utilisateur n'est pas connecté, afficher un message d'erreur
        throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un membre.');
    }

    // Récupérer l'ID de l'équipe depuis le formulaire
    $teamId = $request->request->get('teamId');
    if (!$teamId) {
        throw $this->createNotFoundException('Aucune équipe sélectionnée.');
    }

    // Récupérer l'équipe
    $team = $teamRepository->find($teamId);
    if (!$team) {
        // Si l'équipe n'existe pas, afficher un message d'erreur
        throw $this->createNotFoundException('Équipe introuvable.');
    }

    // Vérifier si l'utilisateur qui ajoute est membre de l'équipe
    if (!$team->getMembership()->contains($currentUser)) {
        // Si l'utilisateur n'est pas membre de l'équipe, afficher un message d'erreur
        throw $this->createAccessDeniedException('Seuls les membres de l\'équipe peuvent ajouter des membres.');
    }

    // Récupérer l'utilisateur à ajouter
    $userToAdd = $userRepository->find($userId);
    if (!$userToAdd) {
        // Si l'utilisateur à ajouter n'existe pas, afficher un message d'erreur
        throw $this->createNotFoundException('Utilisateur introuvable.');
    }
    $pseudo = $userToAdd->getPseudo();

    // Vérifier si l'utilisateur est déjà dans l'équipe
    if ($team->getMembership()->contains($userToAdd)) {
        // Si l'utilisateur est deja membre de l'équipe, afficher un message d'erreur
        $this->addFlash('warning', 'Cet utilisateur est déjà membre de l\'équipe.');
        return $this->redirectToRoute('app_profile', ['pseudo' => $pseudo]);
    }

    // Ajouter l'utilisateur à l'équipe
    $team->addMembership($userToAdd);
    $entityManager->persist($team);
    $entityManager->flush();
    // Afficher un message de confirmation
    $this->addFlash('success', 'Utilisateur ajouté à l\'équipe ' . $team->getName() . '.');
    return $this->redirectToRoute('app_profile', ['pseudo' => $pseudo]);

    }

}
