<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MainMenu
{
    public function __construct(
        private readonly FactoryInterface $factory,
    ) {
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'navbar-nav  mb-2 mb-lg-0');

        $menu->addChild('Home', [
            'route' => 'home',
            'attribute' => 'nav-link',
            'label' => 'Home',
            'attributes' => ['class' => 'nav-item py-3'],
            'labelAttributes' => ['icon' => 'fa-home'],
            'linkAttributes' => ['class' => 'nav-link'],
        ]);

        $menu->addChild('Property', [
            'route' => 'propertyList',
            'attribute' => 'nav-link',
            'label' => 'Properties',
            'attributes' => ['class' => 'nav-item dropdown py-3'],
            'labelAttributes' => ['icon' => 'fa-building'],
            'linkAttributes' => ['class' => 'nav-link'],
        ]);

        $menu->addChild('Property Agents', [
            'route' => 'propertyAgents',
            'attribute' => 'nav-link',
            'label' => 'Property Agents',
            'attributes' => ['class' => 'nav-item dropdown py-3'],
            'labelAttributes' => ['icon' => 'fa-users'],
            'linkAttributes' => ['class' => 'nav-link'],
        ]);

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
