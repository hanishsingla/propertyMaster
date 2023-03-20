<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class HeaderMenu
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
        $currentRoute = $options['current_route'] ?? '';

        $menu->addChild('Home', [
            'route' => 'app_index',
            'attribute' => 'nav-link',
            'label' => 'Home',
            'attributes' => ['class' => 'nav-item'],
        ])->setLinkAttribute('class', 'nav-link');

        $menu->addChild('Properties', [
            'route' => 'app_property',
            'attributes' => ['class' => 'nav-item'],
        ])->setLinkAttribute('class', 'nav-link');

        $menu->addChild('Agents', [
            'route' => 'app_property',
            'attributes' => ['class' => 'nav-item'],
        ])->setLinkAttribute('class', 'nav-link');

        // Set the current menu item as active
        if ($currentRoute === 'app_index') {
            $menu->getChild('Home')->setCurrent(true);
        } elseif ($currentRoute === 'app_property') {
            $menu->getChild('Property')->setCurrent(true);
            $menu->getChild('Agent')->setCurrent(true);
        }

        return $menu;
    }
}