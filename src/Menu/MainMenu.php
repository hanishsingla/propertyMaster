<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class MainMenu
{


    public function __construct(private readonly FactoryInterface $factory, private readonly RequestStack $requestStack, private readonly TranslatorInterface $translator)
    {

    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $trans = $this->translator;
        $request = $this->requestStack;

        $menu->setChildrenAttribute('class', 'navbar-nav  mb-2 mb-lg-0');
        $currentRoute = $this->requestStack->getCurrentRequest();;

        $menu->addChild('Home', [
            'route' => 'app_home',
            'attribute' => 'nav-link',
            'label' => 'Home',
            'attributes' => ['class' => 'nav-item'],
            'labelAttributes' => ['icon' => 'fa-home'],
            'linkAttributes' => ['class' =>   'nav-link'],
        ]);

        $menu->addChild('Properties', [
            'route' => 'app_property',
            'attributes' => ['class' => 'nav-item'],
            'labelAttributes' => ['icon' => 'fa-home'],
            'linkAttributes' => ['class' => 'nav-link'],
        ]);


        return $menu;
    }
}