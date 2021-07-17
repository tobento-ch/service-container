<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Container\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tobento\Service\Container\Container;
use Tobento\Service\Container\ContainerException;
use Tobento\Service\Container\NotFoundException;
use Tobento\Service\Container\Test\Mock\{
    Foo,
    Bar,
    Baz,
    Methods,
    Invokable,
    FooInterface,
    WithBuildInParameter,
    WithBuildInParameterOptional,
    WithBuildInParameterAllowsNull,
    WithBuildInParameterAndClasses,
    WithParameter,
    WithParameters,
    WithoutParameters,
    WithUnionParameter,
    WithUnionParameterAllowsNull,
    WithUnionParameterAllowsNullNotFound
};
use stdClass;

/**
 * ContainerCallTest tests
 */
class ContainerCallTest extends TestCase
{    
    public function testThrowsContainerExceptionIfNotCallable()
    {
        $this->expectException(ContainerException::class);
        
        (new Container())->call(WithBuildInParameter::class);
    }

    public function testStringClassMethodSyntax()
    {
        $this->assertSame(
            'withoutParameters',
            (new Container())->call(
                'Tobento\Service\Container\Test\Mock\Methods::withoutParameters'
            )
        );
    }
    
    public function testClosure()
    {
        $this->assertSame(
            'hellow',
            (new Container())->call(
                function() {
                    return 'hellow';
                }
            )
        );
    }
    
    public function testClassGetsInvoked()
    {
        $this->assertSame(
            'invoked',
            (new Container())->call(
                Invokable::class
            )
        );
    }    
    
    public function testClosureWithParametersGetsAutowired()
    {
        $this->assertInstanceof(
            Foo::class,
            (new Container())->call(
                function(Foo $foo) {
                    return $foo;
                }
            )
        );
    }
    
    public function testClosureWithParametersUsesSetParamaters()
    {
        $foo = new Foo();
        
        $this->assertSame(
            $foo,
            (new Container())->call(
                function(Foo $foo) {
                    return $foo;
                },
                [$foo]
            )
        );
    }
    
    public function testArrayWithClassInstance()
    {
        $this->assertSame(
            'withoutParameters',
            (new Container())->call(
                [new Methods(new Baz()), 'withoutParameters']
            )
        );
    }
    
    public function testArrayWithClassName()
    {
        $this->assertSame(
            'withoutParameters',
            (new Container())->call(
                [Methods::class, 'withoutParameters']
            )
        );
    }
    
    public function testThrowsContainerExceptionIfParameterIsNotResolvable()
    {
        $this->expectException(ContainerException::class);

        (new Container())->call(
            [Methods::class, 'withBuildInParameter']
        );
    }
    
    public function testThrowsContainerExceptionIfMethodIsPrivate()
    {
        $this->expectException(ContainerException::class);

        (new Container())->call(
            [Methods::class, 'withPrivateMethod']
        );
    }    
    
    public function testWithBuildInParameter()
    {        
        $this->assertSame(
            'welcome',
            (new Container())->call(
                [Methods::class, 'withBuildInParameter'],
                ['welcome']
            )
        );
    }
    
    public function testWithBuildInParameterAllowsNull()
    {        
        $this->assertSame(
            null,
            (new Container())->call(
                [Methods::class, 'withBuildInParameterAllowsNull'],
            )
        );
    } 
    
    public function testWithBuildInParameterAllowsNullButUsesParam()
    {        
        $this->assertSame(
            'welcome',
            (new Container())->call(
                [Methods::class, 'withBuildInParameterAllowsNull'],
                ['welcome']
            )
        );
    }
    
    public function testWithBuildInParameterOptional()
    {        
        $this->assertSame(
            null,
            (new Container())->call(
                [Methods::class, 'withBuildInParameterOptional']
            )
        );
    }
    
    public function testWithBuildInParameterOptionalButUsesParam()
    {        
        $this->assertSame(
            'welcome',
            (new Container())->call(
                [Methods::class, 'withBuildInParameterOptional'],
                ['welcome']
            )
        );
    }
 
    public function testWithParameter()
    {
        $foo = new Foo();
        
        $this->assertSame(
            $foo,
            (new Container())->call(
                [Methods::class, 'withParameter'],
                [$foo]
            )
        );
    } 
    
    public function testWithParameterGetsAutowired()
    {        
        $this->assertInstanceof(
            Foo::class,
            (new Container())->call(
                [Methods::class, 'withParameter']
            )
        );
    }
}