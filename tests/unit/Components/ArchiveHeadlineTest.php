<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit\Components;

use ItalyStrap\Components\ArchiveHeadline;
use ItalyStrap\Tests\Unit\UI\Components\CommonRenderViewBlockTestTrait;
use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\UI\Components\ComponentInterface;

class ArchiveHeadlineTest extends UnitTestCase
{
    use CommonRenderViewBlockTestTrait;

    protected function makeInstance(): ArchiveHeadline
    {
        $sut = new ArchiveHeadline($this->makeViewBlock(), $this->makeGlobalDispatcher());
        $this->assertInstanceOf(ComponentInterface::class, $sut, '');
        return $sut;
    }

    public function testItShouldDisplay()
    {

        $this->defineFunction('is_archive', static fn() => true);

        $this->defineFunction('is_search', static fn() => true);

        $this->defineFunction('is_author', static fn() => false);

        $sut = $this->makeInstance();
        $this->assertTrue($sut->shouldDisplay(), '');
    }
}
