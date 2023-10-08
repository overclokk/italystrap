<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit\UI\Infrastructure;

use ItalyStrap\Empress\Extension;
use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\UI\Components\Main\Main;
use ItalyStrap\UI\Infrastructure\ComponentSubscriberExtension;
use Prophecy\Argument;

class ComponentSubscriberExtensionTest extends UnitTestCase
{
    protected function makeInstance(): ComponentSubscriberExtension
    {
        $sut = new ComponentSubscriberExtension($this->makeSubscriberRegister(), $this->makeListenerRegister());
        $this->assertInstanceOf(Extension::class, $sut, '');
        return $sut;
    }

    public function testItShouldHaveName()
    {
        $sut = $this->makeInstance();
        $this->assertSame(ComponentSubscriberExtension::class, $sut->name());
    }

    public function testItShouldExecute()
    {
        $this->markTestSkipped('TODO');
        $sut = $this->makeInstance();

        $this->listenerRegister->addListener(
            Argument::type('string'),
            Argument::type('callable'),
            Argument::type('int'),
            Argument::type('int')
        )->shouldBeCalledOnce();

        $sut->execute($this->makeAurynConfigInterface());
    }

    public function testItShouldWalk()
    {
        $sut = $this->makeInstance();
        $className = 'ClassName';
        $index_or_optionName = 0;

        $class = $this->prophet->prophesize(Main::class);

        $this->injector->share($className)->willReturn($this->injector);
        $this->injector->proxy($className, Argument::type('callable'))->willReturn($this->injector);
        $this->injector->make($className)->willReturn($class);

        $class->shouldDisplay()->willReturn(true);

        $this->subscriberRegister->addSubscriber($class)->shouldBeCalledOnce();

        $sut($className, $index_or_optionName, $this->makeInjector());
    }
}
