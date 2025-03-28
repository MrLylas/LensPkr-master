<?php

namespace App\Controller\Admin;

use App\Entity\Ask;
use App\Entity\ContractType;
use App\Entity\Job;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Level;
use App\Entity\Skill;
use App\Entity\Message;
use App\Entity\Project;
use App\Entity\UserSkill;
use App\Entity\Speciality;
use App\Entity\ProjectImage;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('LensPkr Master 1');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Projects', 'fas fa-list', Project::class);
        yield MenuItem::linkToCrud('Project Images', 'fas fa-list', ProjectImage::class);
        yield MenuItem::linkToCrud('Asks', 'fas fa-list', Ask::class);
        yield MenuItem::linkToCrud('Images', 'fas fa-list', Image::class);
        yield MenuItem::linkToCrud('Jobs', 'fas fa-list', Job::class);
        yield MenuItem::linkToCrud('Levels', 'fas fa-list', Level::class);
        yield MenuItem::linkToCrud('Messages', 'fas fa-list', Message::class);
        yield MenuItem::linkToCrud('Skills', 'fas fa-list', Skill::class);
        yield MenuItem::linkToCrud('Specialities', 'fas fa-list', Speciality::class);
        yield MenuItem::linkToCrud('Teams', 'fas fa-list', Team::class);
        yield MenuItem::linkToCrud('User Skills', 'fas fa-list', UserSkill::class);
        yield MenuItem::linkToCrud('Contract Types', 'fas fa-list', ContractType::class);
    }
}
