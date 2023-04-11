<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class MainMenu
{


    public function __construct(
        private readonly FactoryInterface    $factory,
        private readonly RequestStack        $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly Security            $security
    )
    {
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $trans = $this->translator;
        $request = $this->requestStack;
        $security = $this->security;

        $menu->setChildrenAttribute('class', 'navbar-nav  mb-2 mb-lg-0');
        $currentRoute = $this->requestStack->getCurrentRequest();;

        $menu->addChild('Home', [
            'route' => 'home',
            'attribute' => 'nav-link',
            'label' => 'Home',
            'attributes' => ['class' => 'nav-item py-3'],
            'labelAttributes' => ['icon' => 'fa-home'],
            'linkAttributes' => ['class' => 'nav-link'],
        ]);

        $dropdown = $menu->addChild('Property', [
            'route' => 'propertyList',
            'attributes' => ['class' => 'nav-item dropdown py-3'],
            'labelAttributes' => ['icon' => 'fa-building'],
            'linkAttributes' => ['class' => 'nav-link dropdown-toggle',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'aria-expanded' => 'false'
            ],
        ]);

        $dropdown->addChild('Property List', [
            'route' => 'propertyList',
            'labelAttributes' => ['icon' => 'fa-list'],
            'linkAttributes' => ['class' => 'dropdown-item'],
        ]);

        $dropdown->addChild('Property Type', [
            'route' => 'propertyType',
            'labelAttributes' => ['icon' => 'fa-building-columns'],
            'linkAttributes' => ['class' => 'dropdown-item'],
        ]);

        $dropdown->addChild(' Property Agent', [
            'route' => 'propertyAgent',
            'labelAttributes' => ['icon' => 'fa-users'],
            'linkAttributes' => ['class' => 'dropdown-item'],
        ]);

        $dropdown->setChildrenAttributes(['class' => 'dropdown-menu']);

        $menu->addChild('About', [
            'label' => 'About',
            'route' => 'about',
            'attributes' => ['class' => 'nav-item py-3'],
            'labelAttributes' => ['icon' => 'fa-circle-info'],
            'linkAttributes' => ['class' => 'nav-link'],
        ]);

        $menu->addChild('Contact', [
            'label' => 'Contact',
            'route' => 'contact',
            'attributes' => ['class' => 'nav-item py-3'],
            'labelAttributes' => ['icon' => 'fa-location-dot'],
            'linkAttributes' => ['class' => 'nav-link'],
        ]);



        return $menu->setChildrenAttributes(['class' => 'navbar-nav  mb-2 mb-lg-0']);
    }
}