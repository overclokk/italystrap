<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Config\ConfigInterface;
use Prophecy\Prophecy\ObjectProphecy;
use UnitTester;
use function tad\FunctionMockerLe\undefineAll;

abstract class AssetBase extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

	/**
	 * @var ObjectProphecy
	 */
	protected $config;

	protected $apply_filters_called = 0;

	/**
	 * @return ConfigInterface
	 */
	public function getConfig(): ConfigInterface {
		return $this->config->reveal();
	}

	protected function _before()
	{
		$this->config = $this->prophesize( ConfigInterface::class);

		\tad\FunctionMockerLe\define('apply_filters', function ($event_name, $arg) {
			$this->apply_filters_called++;
			return $arg;
		});
	}

    protected function _after()
    {
    	undefineAll([
			'apply_filters',
		]);
    }

    abstract protected function getInstance();

	/**
	 * @test
	 */
    public function instanceOk()
    {
		$sut = $this->getInstance();
		$this->assertInstanceOf( Asset::class, $sut, '' );
    }
}