<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\NewTeamType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TeamController extends AbstractController
{
    #[Route('/team', name: 'team_feed')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $teams = $entityManager->getRepository(Team::class)->findAll();

        return $this->render('team/index.html.twig', [
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
}
