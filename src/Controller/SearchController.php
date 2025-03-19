<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request, ProjectRepository $projectRepository, TeamRepository $teamRepository, UserRepository $userRepository,PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('q'); // Récupère la requête de recherche
    
        $projects = $projectRepository->searchProjects($query);
        $teams = $teamRepository->searchTeams($query);
        $userList = $userRepository->searchUsers($query);
    
        $projects = $paginator->paginate(
            $projects,
            $request->query->getInt('page', 1),
            5
        );
    
        $teams = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            5
        );
    
        $userList = $paginator->paginate(
            $userList,
            $request->query->getInt('page', 1),
            5
        );
    
        return $this->render('search/results.html.twig', [
            'projects' => $projects,
            'teams' => $teams,
            'query' => $query,
            'userList' => $userList
        ]);
    }    
}
