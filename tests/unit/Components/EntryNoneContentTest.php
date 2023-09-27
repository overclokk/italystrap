<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit\Components;

use ItalyStrap\Components\ComponentInterface;
use ItalyStrap\Components\EntryNoneContent;
use ItalyStrap\Config\ConfigNotFoundProvider;
use ItalyStrap\Tests\UnitTestCase;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

class EntryNoneContentTest extends UnitTestCase
{
    protected function getInstance(): EntryNoneContent
    {
        $sut = new EntryNoneContent($this->makeConfig(), $this->makeView(), $this->makeGlobalDispatcher());
        $this->assertInstanceOf(ComponentInterface::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldLoad()
    {
        $sut = $this->getInstance();
        $this->assertTrue($sut->shouldDisplay(), '');
    }

    /**
     * @test
     */
    public function itShouldDisplay()
    {
        $sut = $this->getInstance();

        $this->config->get(ConfigNotFoundProvider::CONTENT)->willReturn('Some content')->shouldBeCalledOnce();

        $this->view->render('posts/none/content', Argument::type('array'))->willReturn('posts/none/content');

        $this->defineFunction('do_blocks', static function (string $block) {
            Assert::assertEquals('posts/none/content', $block, '');
            return 'from do_block';
        });

        $this->expectOutputString('from do_block');
        $sut->display();
    }
}
