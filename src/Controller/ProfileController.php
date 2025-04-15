<?php

namespace App\Controller;

use App\Entity\Ask;
use App\Entity\Job;
use App\Entity\User;
use App\Entity\Level;
use App\Entity\Skill;
use App\Form\UserType;
use App\Entity\Project;
use App\Entity\UserSkill;
use App\Entity\Speciality;
use App\Form\UserSkillType;
use App\Service\FileUploader;
use App\Form\ProjectFilterType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use App\Repository\UserSkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class ProfileController extends AbstractController
{
    #[Route('/profile/users', name: 'list_users')]
    public function listUsers(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $userRepository->findAll();
        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('profile/users.html.twig', [
            'meta_description' => 'User list',
            'users' => $users,
        ]);
    }
    #[Route('/profile/my-profile', name: 'my_profile')]
    public function myProfile(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $projects = $user->getProjects();
        $skills = $user->getUserSkills();
        $jobs = $user->getJobs();


        $projects = $paginator->paginate(
            $projects,
            $request->query->getInt('page', 1),
            3
        );


        return $this->render('profile/index.html.twig', [
            'meta_description' => 'Profile de ' . $user->getPseudo(),
            'user' => $user,
            'projects' => $projects,
            'skills' => $skills,
            'jobs' => $jobs
        ]);
    }

    #[Route('/profile/edit', name: 'edit_profile')]
    public function editProfile(EntityManagerInterface $entityManager, FileUploader $fileUploader, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non rencontré');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user_profilePic = $form->get('profile_pic')->getData();
            if ($user_profilePic) {
                $fileName = $fileUploader->upload($user_profilePic);
                $user->setProfilePic($fileName);
            }
            $userBanner = $form->get('banner')->getData();
            if ($userBanner) {
                $fileName = $fileUploader->upload($userBanner);
                $user->setBanner($fileName);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('my_profile');
        }
        return $this->render('profile/userEdit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/profile/{pseudo}', name: 'app_profile')]
    public function index(string $pseudo, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(['pseudo' => $pseudo]);
        $skills = $user->getUserSkills();
        $projects = $user->getProjects();

        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }
        $projects = $paginator->paginate(
            $projects,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('profile/index.html.twig', [
            'meta_description' => 'Profile de ' . $user->getPseudo(),
            'projects' => $projects,
            'user' => $user,
            'skills' => $skills
        ]);
    }

    #[Route('/profile/edit_skill', name: 'edit_skill')]
    public function editSkill(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $skills = $entityManager->getRepository(Skill::class)->findAll();
        $levels = $entityManager->getRepository(Level::class)->findAll();

        return $this->render('profile/edit.html.twig', [
            'meta_description' => 'Edit profile',
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
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur non connecté'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['skill_id'], $data['level_id'])) {
            return new JsonResponse(['success' => false, 'message' => 'Données invalides'], 400);
        }

        $skill = $entityManager->getRepository(Skill::class)->find($data['skill_id']);
        $level = $entityManager->getRepository(Level::class)->find($data['level_id']);

        if (!$skill || !$level) {
            return new JsonResponse(['success' => false, 'message' => 'Compétence ou niveau introuvable'], 404);
        }

        $existingSkill = $entityManager->getRepository(UserSkill::class)->findOneBy([
            'user' => $user,
            'skill' => $skill
        ]);

        if ($existingSkill) {
            return new JsonResponse(['success' => false, 'message' => 'Cette compétence est déjà ajoutée'], 400);
        }

        $userSkill = new UserSkill();
        $userSkill->setUser($user);
        $userSkill->setSkill($skill);
        $userSkill->setLevel($level);

        $entityManager->persist($userSkill);
        $entityManager->flush();

        return new JsonResponse([
            'meta_description' => 'add skill',
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
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur non connecté'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $skillId = $data['skill_id'] ?? null;

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
    public function show_skill(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $skills = $entityManager->getRepository(UserSkill::class)->findSkillNotInUser($user->getId());


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



    #[Route('/profile/job/answer/{id}', name: 'app_answers')]
    public function showAnswers(Job $job): Response{

        $user = $this->getUser();

        return $this->render('profile/answers.html.twig', [
            'meta_description' => 'Answers',
            'user' => $user,
            'job' => $job
        ]);
    }

    #[Route('/profile/delete-account', name: 'app_delete_account')]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('User not found');
        }

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_token');
            if ($this->isCsrfTokenValid('delete-account', $token)) {
                $entityManager->remove($user->getProjects());
                $entityManager->remove($user->getJobs());
                $entityManager->remove($user->getAnswers());
                $entityManager->remove($user->getUserSkills());
                $entityManager->remove($user);
                $entityManager->flush();

                // Logout the user after deletion
                $this->get('security.token_storage')->setToken(null);
                $request->getSession()->invalidate();

                return $this->render('security/login.html.twig');
            }
        }

        return $this->render('security/delete_account.html.twig');
    }
}





 