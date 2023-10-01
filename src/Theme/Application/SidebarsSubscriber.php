<?php

declare(strict_types=1);

namespace ItalyStrap\Theme\Application;

use ItalyStrap\Config\ConfigInterface as Config;
use ItalyStrap\Event\SubscriberInterface;
use ItalyStrap\HTML\Tag;

use function array_filter;
use function array_merge;
use function register_sidebar;

/**
 * There are a standard sidebar and 4 footer dynamic sidebars
 */
final class SidebarsSubscriber implements SubscriberInterface
{
    public const NAME = 'name';
    public const ID = 'id';
    public const DESCRIPTION = 'description';
    public const CLASS_NAME = 'class';
    public const BEFORE_WIDGET = 'before_widget';
    public const AFTER_WIDGET = 'after_widget';
    public const BEFORE_TITLE = 'before_title';
    public const AFTER_TITLE = 'after_title';

    private Tag $tag;

    /**
     * @var array<string>
     */
    private array $registered_sidebars;
    public function getSubscribedEvents(): iterable
    {
        yield 'widgets_init'            => 'register';
        yield 'dynamic_sidebar_before'  => 'parseDynamicSidebarBefore';
    }

    private Config $config;

    public function __construct(Config $config, Tag $tag)
    {
        $this->config = $config;
        $this->tag = $tag;
        $this->registered_sidebars = [];
    }

    public function register(): void
    {
        foreach ((array)$this->config->get(self::class, []) as $key => $sidebar) {
            $this->registered_sidebars[ $key ] = register_sidebar($sidebar);
        }
    }

    /**
     * @param int|string $index
     */
    public function parseDynamicSidebarBefore($index): void
    {
        /** @var array<string> $wp_registered_sidebars */
        global $wp_registered_sidebars;
        $wp_registered_sidebars[ $index ] = array_merge(
            (array)$wp_registered_sidebars[$index],
            array_filter($this->getDefault($index))
        );
    }

    /**
     * @param int|string $id
     */
    private function getDefault($id): array
    {
        $widget_context = $id . '-widget';
        $title_context = $id . '-title';

        return [
            self::NAME => '',
            self::ID => '',
            self::DESCRIPTION => '',
            self::CLASS_NAME => '',
            self::BEFORE_WIDGET => $this->tag->open(
                $widget_context,
                'div',
                ['id' => '%1$s', 'class' => 'widget %2$s']
            ),
            self::AFTER_WIDGET => $this->tag->close($widget_context),
            self::BEFORE_TITLE => $this->tag->open(
                $title_context,
                'h3',
                ['class' => 'widgettitle widget-title']
            ),
            self::AFTER_TITLE => $this->tag->close($title_context),
        ];
    }

    private function defaultSidebarConfig(array $sidebar): array
    {
        $defaults = $this->getDefault($sidebar[ 'id' ]);
        return array_merge($defaults, $sidebar);
    }
}
