<?php

namespace App\Controller;

use App\Entity\Ask;
use App\Entity\Job;
use App\Entity\User;
use App\Entity\Level;
use App\Entity\Skill;
use App\Form\UserType;
use App\Entity\UserSkill;
use App\Entity\Speciality;
use App\Form\UserSkillType;
use App\Service\FileUploader;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use App\Repository\UserSkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function index(user $user): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'id'=> $user->getId(),
        ]);
    }

//     #[Route('/profile/edit/{id}', name: 'edit_user')]
//     public function edit_user(Request $request, User $user, FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
//     {
//     // Création du formulaire
//     $userForm = $this->createForm(UserType::class, $user);
//     $userForm->handleRequest($request);
//     // Si le formulaire est soumis et valide
//     if ($userForm->isSubmitted() && $userForm->isValid()) {
//         $uploadedProfilePic = $userForm->get('profile_pic')->getData();
//         $uploadedBanner = $userForm->get('banner')->getData();

//         // Si la photo de profil existe, supprimer le fichier existant
//         if ($user->getProfilePic() !== null) {
//             $path = $this->getParameter('upload_directory') . "/" . $user->getProfilePic();
//             if (file_exists($path)) {
//                 unlink($path);
//             }
//         }
//         if ($uploadedProfilePic) {
//             $newFilename = $fileUploader->upload($uploadedProfilePic);
//             $user->setProfilePic($newFilename);
//         }

//         // Si la bannière existe, supprimer le fichier existant
//         if ($user->getBanner() !== null) {
//             $path = $this->getParameter('upload_directory') . "/" . $user->getBanner();
//             if (file_exists($path)) {
//                 unlink($path);
//             }
//         }

//         // Si une nouvelle bannière a été upload
//         if ($uploadedBanner) {
//             $newBanner = $fileUploader->upload($uploadedBanner);
//             $user->setBanner($newBanner);
//         }

//         // Enregistrer les modifications
//         $entityManager->persist($user);
//         $entityManager->flush();
        
//         // Redirection
//         return $this->redirectToRoute('app_user_skill', ['id' => $this->getUser()->getId()]);
//     }
//     return $this->render('/profile/edit.html.twig', [
//         'UserForm' => $userForm->createView(),
//     ]);
// }

#[Route('/profile/edit/{id}', name: 'edit_skill')]
    public function edit_user(User $user, EntityManagerInterface $entityManager): Response
    {
        $skills = $entityManager->getRepository(Skill::class)->findAll();
        $levels = $entityManager->getRepository(Level::class)->findAll();

        return $this->render('/profile/edit.html.twig', [
            'user' => $user,
            'userSkills' => $user->getUserSkills(),
            'skills' => $skills,
            'levels' => $levels,
        ]);
    }

    #[Route('/profile/add-skill', name: 'add_skill', methods: ['POST'])]
    public function addSkill(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $skillId = $request->request->get('skill_id');
        $levelId = $request->request->get('level_id');

        if (!$skillId || !$levelId) {
            return new JsonResponse(['success' => false, 'message' => 'Données manquantes'], 400);
        }

        $skill = $entityManager->getRepository(Skill::class)->find($skillId);
        $level = $entityManager->getRepository(Level::class)->find($levelId);

        if (!$skill || !$level) {
            return new JsonResponse(['success' => false, 'message' => 'Compétence ou niveau invalide'], 404);
        }

        $userSkill = new UserSkill();
        $userSkill->setUser($user);
        $userSkill->setSkill($skill);
        $userSkill->setLevel($level);

        $entityManager->persist($userSkill);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Compétence ajoutée',
            'skill' => $skill->getSkillName(),
            'level' => $level->getLevelName(),
            'skill_id' => $skill->getId(),
        ]);
    }

    #[Route('/profile/remove-skill', name: 'remove_skill', methods: ['POST'])]
    public function removeSkill(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $skillId = $request->request->get('skill_id');

        if (!$skillId) {
            return new JsonResponse(['success' => false, 'message' => 'ID de compétence manquant'], 400);
        }

        $userSkill = $entityManager->getRepository(UserSkill::class)->findOneBy([
            'user' => $user,
            'skill' => $skillId
        ]);

        if (!$userSkill) {
            return new JsonResponse(['success' => false, 'message' => 'Compétence non trouvée'], 404);
        }

        $entityManager->remove($userSkill);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Compétence supprimée']);
    }
 

    #[Route('/profile/skill/{id}', name: 'app_user_skill')]
    public function show_skill(User $user, EntityManagerInterface $entityManager): Response
    {

        $skills = $entityManager->getRepository(UserSkill::class)->findSkillNotInUser($user->getId());


        $user = $this->getUser();
        $userSkills = $user->getUserSkills();
        $user_id = $user->getId();
        

        return $this->render('profile/skill.html.twig', [
            'controller_name' => 'UserSkillController',
            'user' => $user,
            'userSkills' => $userSkills,
            'skills' => $skills,
            'id' => $user_id,
        ]);
    }

    // #[Route('/profile/job/{id}', name: 'skill_by_user')]
    // public function show_skill_by_user(User $user, EntityManagerInterface $entityManager): Response
    // {
    //     $skills = $entityManager->getRepository(UserSkill::class)->findSkillNotInUser($user->getId());


    //     $user = $this->getUser();
    //     $userSkills = $user->getUserSkills();
    //     $user_id = $user->getId();
        

    //     return $this->render('profile/skill.html.twig', [
    //         'controller_name' => 'UserSkillController',
    //         'user' => $user,
    //         'userSkills' => $userSkills,
    //         'skills' => $skills,
    //         'id' => $user_id,
    //     ]);
    // }

    #[Route('/profile/job/answer/{id}', name: 'app_answers')]
    public function showAnswers(Job $job): Response{

        $user = $this->getUser();

        // $asks = $entityManager->getRepository(Ask::class)->findBy(['job' => $job->getId()]);


        return $this->render('profile/answers.html.twig', [
            'controller_name' => 'UserSkillController',
            'user' => $user,
            // 'asks' => $asks,
            'job' => $job
        ]);
    }
}