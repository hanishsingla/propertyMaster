<?php

namespace App\Controller\Admin;

use App\Entity\Agent;
use App\Entity\Category;
use App\Entity\Property;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
class UserDashboard extends AbstractDashboardController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/userDash', name: 'userDash')]
    public function userDash(): Response
    {
        return $this->render('userDashboard/user_dashboard.html.twig');
    }
    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        return [

            yield MenuItem::section('Blog'),
            yield MenuItem::linkToCrud('Agent', 'fa fa-user-cog', Agent::class),
            yield MenuItem::linkToCrud('Property', 'fa fa-home', Property::class),
        ];
    }
}