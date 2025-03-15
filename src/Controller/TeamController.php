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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TeamController extends AbstractController
{
    #[Route('/team', name: 'team_feed')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $teams = $entityManager->getRepository(Team::class)->findAll();

        return $this->render('team/recent.html.twig', [
            'controller_name' => 'TeamController',
            'teams' => $teams
        ]);
    }

    #[Route('/team/new', name: 'new_team')]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $newTeam = new Team();
        $form = $this->createForm(NewTeamType::class, $newTeam);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $teamPic = $form->get('team_pic')->getData();
            $bannerPic = $form->get('team_banner')->getData();

            if ($teamPic) {
                $fileName = $fileUploader->upload($teamPic);
                $newTeam->setTeamPic($fileName);
            }
            if ($bannerPic) {
                $fileName = $fileUploader->upload($bannerPic);
                $newTeam->setTeamBanner($fileName);
            }
            $newTeam = $form->getData();
            $newTeam->setCreatedAt(new \DateTimeImmutable());
            $newTeam->setCreator($this->getUser());
            $newTeam->addMembership($this->getUser());

            $entityManager->persist($newTeam);
            $entityManager->flush();

            return $this->redirectToRoute('team_feed');
        }

        return $this->render('team/new.html.twig', [
            'controller_name' => 'TeamController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/team/{id}', name: 'team_show')]
    public function show(ProjectRepository $projectRepository,EntityManagerInterface $entityManager, int $id): Response
    {
        $team = $entityManager->getRepository(Team::class)->find($id);
        $followers = $team->getFollow();
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
