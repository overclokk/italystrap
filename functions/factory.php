<?php
/**
 * Get the Injector instance
 */
declare(strict_types=1);

namespace ItalyStrap\Factory;

use Auryn\{ConfigException, InjectionException};
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\Empress\Injector;
use ItalyStrap\Config\Config;
use ItalyStrap\View\View;

if ( ! function_exists( '\ItalyStrap\Factory\injector' ) ) {

	/**
	 * @return Injector
	 * @throws ConfigException
	 */
	function injector(): \Auryn\Injector {

		/**
		 * Injector from ACM if is active
		 */
		$injector = apply_filters( 'italystrap_injector', false );

		if ( ! $injector ) {
			$injector = new Injector();
			$injector->share($injector);
			add_filter( 'italystrap_injector', function () use ( $injector ) {
				return $injector;
			} );
		}

		return $injector;
	}
}

if ( ! function_exists( '\ItalyStrap\Factory\get_config' ) ) {

	/**
	 * @return Config
	 */
	function get_config() : Config {

		static $config = null;

		if ( ! $config ) {
			try {
				$config = injector()
					->alias( ConfigInterface::class,  Config::class )
					->share( Config::class )
					->make( Config::class );
			} catch ( ConfigException $configException ) {
				echo $configException->getMessage();
			} catch ( InjectionException $injectionException ) {
				echo $injectionException->getMessage();
			} catch ( \Exception $exception ) {
				echo $exception->getMessage();
			}
		}

		return $config;
	}
}

if ( ! function_exists( '\ItalyStrap\Factory\get_view' ) ) {

	/**
	 * @return View
	 */
	function get_view(): View {

		static $view = null;

		if ( ! $view ) {
			try {
				$view = injector()
					->share( View::class )
					->make( View::class );
			} catch ( ConfigException $configException ) {
				echo $configException->getMessage();
			} catch ( InjectionException $injectionException ) {
				echo $injectionException->getMessage();
			} catch ( \Exception $exception ) {
				echo $exception->getMessage();
			}
		}

		return $view;
	}
}
