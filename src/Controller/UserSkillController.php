<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Level;
use App\Entity\UserSkill;
use App\Entity\Speciality;
use App\Form\UserSkillType;
use App\Repository\LevelRepository;
use App\Repository\UserSkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserSkillController extends AbstractController
{


    // #[Route('/profile/skill/{id}', name: 'app_user_skill')]
    // public function list_skill(User $user, EntityManagerInterface $entityManager): Response
    // {
    //     // UserSkill $user_skill, 
    //     $user_skill = $entityManager->getRepository(UserSkill::class)->findBy(['user' => $user->getId()]);
        
    //     $skills = $entityManager->getRepository(UserSkill::class)->findSkillNotInUser($user_skill);

    //     return $this->render('profile/skill.html.twig',[
    //         'skills' => $skills,
    //         'user' => $user
    //     ]);
    // }

    // #[Route('/profile/skill/{id}', name: 'edit_skill')]
    // public function editSkill(UserSkill $userSkill, Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $availableSkills = $entityManager->getRepository(UserSkill::class)->findSkillNotInUser($userSkill);
    //     $levels = $entityManager->getRepository(Level::class)->findAll();
    //     $specialities = $entityManager->getRepository(Speciality::class)->findAll();

    //     $skillForm = $this->createForm(UserSkillType::class, $userSkill);
    //     $skillForm->handleRequest($request);

    //     if ($skillForm->isSubmitted() && $skillForm->isValid()) {
    //         $entityManager->persist($userSkill);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_user_skill');
    //     }

    //     return $this->render('profile/edit_skill.html.twig', [
    //         'skillForm' => $skillForm,
    //         'userSkill' => $userSkill,
    //         'availableSkills' => $availableSkills,
    //         'levels' => $levels,
    //         'specialities' => $specialities
    //     ]);
    // }

    // #[Route('/profile/skill/{id}', name: 'edit_skill')]
    // public function edit_skill(UserSkill $user_skill, Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $user_skill = $entityManager->getRepository(UserSkill::class)->findBy(['user' => $user->getId()]);

    //     $SkillForm = $this->createForm(UserSkillType::class, $user_skill);
    //     $SkillForm->handleRequest($request);



    //     return $this->render('profile/test.html.twig', [
    //         'formSkill' => $SkillForm,
    //         'user_skill' => $user_skill
    //     ]);
    // }

    #[Route('/profile/skill/{id}', name: 'delete_skill')]
    public function delete_skill(UserSkill $user_skill, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user_skill);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_skill');
    }

    #[Route('/profile/skill/{id}', name: 'add_skill')]
    public function add_skill(User $user, UserSkill $user_skill, EntityManagerInterface $entityManager): Response
    {
        $user->addUserSkill($user_skill);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_skill');
    }




}
