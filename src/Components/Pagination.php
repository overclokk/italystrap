<?php
declare(strict_types=1);

namespace ItalyStrap\Components;

use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Event\SubscriberInterface;
use ItalyStrap\View\ViewInterface;

class Pagination implements SubscriberInterface, ComponentInterface {

	use SubscribedEventsAware;

	const EVENT_NAME = 'italystrap_after_loop';
	const EVENT_PRIORITY = 10;

	private ConfigInterface $config;
	private ViewInterface $view;

	public function __construct( ConfigInterface $config, ViewInterface $view  ) {
		$this->config = $config;
		$this->view = $view;
	}

	public function shouldDisplay(): bool {
		return ! \is_404();
	}

	public function display(): void {
		echo \do_blocks( $this->view->render( 'temp/pagination', [] ) );
	}
}