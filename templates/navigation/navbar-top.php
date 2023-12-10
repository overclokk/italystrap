<?php

declare(strict_types=1);

namespace ItalyStrap\Headers;

use ItalyStrap\Navigation\Infrastructure\BootstrapNavMenu;

?><nav id="top-nav" class="top-nav">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                /**
                 * Menù per i contatti
                 */
                \wp_nav_menu(
                    [
                        'theme_location'    => 'info-menu',
                        'depth'             => 1,
                        'container'         => 'div',
                        'container_class'   => 'pull-left float-left',
                        'fallback_cb'       => false,
                        'menu_class'        => 'list-inline info-menu',
                        'walker'            => new BootstrapNavMenu(),
                    ]
                );
                /**
                 * Menù per i link sociali
                 */
                \wp_nav_menu(
                    [
                        'theme_location'    => 'social-menu',
                        'depth'             => 1,
                        'container'         => 'div',
                        'container_class'   => 'pull-right float-right',
                        'fallback_cb'       => false,
                        'menu_class'        => 'list-inline social-menu',
                        'link_before'       => '<span class="item-title">',
                        'link_after'        => '</span>',
                        'walker'            => new BootstrapNavMenu(),
                    ]
                );
                ?>
            </div>
        </div>
    </div>
</nav>