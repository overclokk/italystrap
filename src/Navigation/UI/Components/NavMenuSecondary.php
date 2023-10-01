<?php

declare(strict_types=1);

namespace ItalyStrap\Navigation\UI\Components;

use ItalyStrap\Components\SubscribedEventsAware;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Navigation\Domain\NavMenu;
use ItalyStrap\Navigation\Domain\NavMenuInterface;
use ItalyStrap\Navigation\Domain\NavMenuLocationInterface;
use ItalyStrap\UI\Components\ComponentInterface;
use ItalyStrap\View\ViewInterface;

class NavMenuSecondary implements ComponentInterface, \ItalyStrap\Event\SubscriberInterface
{
    use SubscribedEventsAware;

    public const EVENT_NAME = 'italystrap_navmenu';
    public const EVENT_PRIORITY = 10;

    private ConfigInterface $config;
    private ViewInterface $view;
    private NavMenu $menu;
    private NavMenuLocationInterface $location;

    public function __construct(
        ConfigInterface $config,
        ViewInterface $view,
        NavMenuInterface $menu,
        NavMenuLocationInterface $location
    ) {
        $this->config = $config;
        $this->view = $view;
        $this->menu = $menu;
        $this->location = $location;
    }

    public function shouldDisplay(): bool
    {
        return $this->location->has(self::class);
    }

    public function display(): void
    {
        echo $this->menu->render([
            NavMenu::MENU_CLASS_NAME => 'nav navbar-nav navbar-right',
            NavMenu::MENU_ID => 'secondary-menu',
            NavMenu::FALLBACK_CB => false,
            NavMenu::THEME_LOCATION => self::class,
        ]);
    }
}
