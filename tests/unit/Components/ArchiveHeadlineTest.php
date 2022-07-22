<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Components;

use ItalyStrap\Components\ComponentInterface;
use ItalyStrap\Components\ArchiveHeadline;
use ItalyStrap\Tests\BaseUnitTrait;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

class ArchiveHeadlineTest extends \Codeception\Test\Unit {

	use BaseUnitTrait;

	protected function getInstance(): ArchiveHeadline {
		$sut = new ArchiveHeadline($this->getConfig(), $this->getView(), $this->getDispatcher());
		$this->assertInstanceOf(ComponentInterface::class, $sut, '');
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldLoad() {

		\tad\FunctionMockerLe\define('is_archive', static function () {
			return true;
		});

		\tad\FunctionMockerLe\define('is_search', static function () {
			return true;
		});

		\tad\FunctionMockerLe\define('is_author', static function () {
			return false;
		});

		$sut = $this->getInstance();
		$this->assertTrue($sut->shouldDisplay(), '');
	}

	/**
	 * @test
	 */
	public function itShouldDisplay() {
		$sut = $this->getInstance();

		$this->view->render( 'misc/archive-headline', Argument::type('array') )->willReturn('misc/archive-headline');

		\tad\FunctionMockerLe\define('do_blocks', static function ( string $block ) {
			Assert::assertEquals('misc/archive-headline', $block, '');
			return 'from do_block';
		});

		$this->expectOutputString('from do_block');
		$sut->display();
	}
}