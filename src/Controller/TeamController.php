<?php

namespace App\Controller;

use Dom\Entity;
use App\Entity\Team;
use App\Form\NewTeamType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\TextUI\XmlConfiguration\File;
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
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $team = $entityManager->getRepository(Team::class)->find($id);

        return $this->render('team/show.html.twig', [
            'controller_name' => 'TeamController',
            'team' => $team
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
}
