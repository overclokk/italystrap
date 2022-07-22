<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Components;

use ItalyStrap\Components\ComponentInterface;
use ItalyStrap\Components\Title;
use ItalyStrap\Tests\BaseUnitTrait;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

class TitleTest extends \Codeception\Test\Unit {

	use BaseUnitTrait;

	protected function getInstance(): Title {
		$sut = new Title($this->getConfig(), $this->getView());
		$this->assertInstanceOf(ComponentInterface::class, $sut, '');
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldLoad() {
		$sut = $this->getInstance();

		\tad\FunctionMockerLe\define('get_post_type', static function () {
			return 'post';
		});

		\tad\FunctionMockerLe\define(
			'post_type_supports',
			static function ( string $post_type, string $feature) {
				Assert::assertEquals('post', $post_type, '');
				return true;
			}
		);

		$this->config->get('post_content_template')->willReturn([]);

		$this->assertTrue($sut->shouldDisplay(), '');
	}

	/**
	 * @test
	 */
	public function itShouldDisplay() {
		$sut = $this->getInstance();

		\tad\FunctionMockerLe\define('is_singular', static function (): bool {
			return true;
		});

		$this->view->render( 'temp/title', Argument::type('array') )->willReturn('title');

		\tad\FunctionMockerLe\define('do_blocks', static function ( string $block ) {
			Assert::assertEquals('title', $block, '');
			return 'from do_block';
		});


		$this->expectOutputString('from do_block');
		$sut->display();
	}
}