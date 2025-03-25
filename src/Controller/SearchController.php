<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\SearchRepository;
use App\Repository\MessageRepository;
use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SearchController extends AbstractController
{
    #[Route('/search/project/search', name: 'project_search')]
    public function searchProject(Request $request, ProjectRepository $projectRepository,PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('q'); // Récupère la requête de recherche
        if ($query == "") {
            return $this->redirectToRoute('recent_project');
        }
    
        $projects = $projectRepository->searchProjects($query);

        $projects = $paginator->paginate(
            $projects, 
            $request->query->getInt('page', 1),
             6);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'query' => $query
        ]);
        
    }

    #[Route('/search/team/search', name: 'team_search')]
    public function searchTeam(Request $request,TeamRepository $teamRepository,PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('q'); // Récupère la requête de recherche
        if ($query == "") {
            return $this->redirectToRoute('recent_team');
        }
    
        $teams = $teamRepository->searchTeams($query);

        $teams = $paginator->paginate(
            $teams, 
            $request->query->getInt('page', 1),
             5);

        return $this->render('team/index.html.twig', [
            'teams' => $teams,
            'query' => $query
        ]);
        
    }

    #[Route('/search/user/search', name: 'user_search')]
    public function searchUser(Request $request,UserRepository $userRepository,PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('q'); // Récupère la requête de recherche
        if ($query == "") {
            return $this->redirectToRoute('list_users');
        }
        $userList = $userRepository->searchUsers($query);

        $userList = $paginator->paginate(
            $userList, 
            $request->query->getInt('page', 1),
             3);

        return $this->render('profile/users.html.twig', [
            'users' => $userList,
            'query' => $query
        ]);
        
    }
    #[Route('/search/message/search', name: 'received_message_search')]
    public function searchReceivedMessage(Request $request,MessageRepository $messageRepository,PaginatorInterface $paginator, int $id): Response
    {
        $id = $this->getUser()->getId();
        $query = $request->query->get('q'); // Récupère la requête de recherche
        if ($query == "") {
            return $this->redirectToRoute('received_message');
        }
    
        $messages = $messageRepository->findReceivedMessage($query);

        $messages = $paginator->paginate(
            $messages, 
            $request->query->getInt('page', 1),
             5);

        return $this->render('message/index.html.twig', [
            'id' => $id,
            'messages' => $messages,
            'query' => $query
        ]);
        
    }

    #[Route('/search/message/search/sent', name: 'sent_message_search')]
    public function searchSentMessage(Request $request,MessageRepository $messageRepository,PaginatorInterface $paginator, int $id): Response
    {
        $id = $this->getUser()->getId();
        $query = $request->query->get('q'); // Récupère la requête de recherche
        if ($query == "") {
            return $this->redirectToRoute('sent_message');
        }
    
        $messages = $messageRepository->findSentMessage($query);

        $messages = $paginator->paginate(
            $messages, 
            $request->query->getInt('page', 1),
             5);

        return $this->render('message/index.html.twig', [
            'id' => $id,
            'messages' => $messages,
            'query' => $query
        ]);
        
    }
}
