<?php

declare(strict_types=1);

namespace ItalyStrap\Navigation\UI\Components;

use ItalyStrap\Components\SubscribedEventsAware;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Event\EventDispatcherInterface;
use ItalyStrap\Event\SubscriberInterface;
use ItalyStrap\UI\Components\ComponentInterface;
use ItalyStrap\View\ViewInterface;

class NavMenuHeader implements ComponentInterface, SubscriberInterface
{
    use SubscribedEventsAware;

    public const EVENT_NAME = 'italystrap_before_navmenu';
    public const EVENT_PRIORITY = 10;

    private ConfigInterface $config;
    private ViewInterface $view;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        ConfigInterface $config,
        ViewInterface $view,
        EventDispatcherInterface $dispatcher
    ) {
        $this->config = $config;
        $this->view = $view;
        $this->dispatcher = $dispatcher;
    }

    public function shouldDisplay(): bool
    {
        return true;
    }

    public function display(): void
    {
        echo $this->view->render('navigation/header', [
            EventDispatcherInterface::class => $this->dispatcher,
        ]);
    }
}
