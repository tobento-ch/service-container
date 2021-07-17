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
 * ContainerMakeTest tests
 */
class ContainerMakeTest extends TestCase
{    
    public function testThrowsContainerExceptionIfNotResolvable()
    {
        $this->expectException(ContainerException::class);
        
        (new Container())->make(WithBuildInParameter::class);
    }    
    
    public function testWithClassName()
    {
        $c = new Container();
        
        $this->assertInstanceOf('stdClass', $c->make('stdClass'));
    }
    
    public function testReturnsNewInstances()
    {
        $c = new Container();
        
        $this->assertFalse($c->make(Foo::class) === $c->make(Foo::class));
    }
    
    public function testUsesParameters()
    {
        $c = new Container();
        
        $foo = new Foo();
        
        $c->set(WithParameter::class)->construct(new Foo());
        
        $resolved = $c->make(WithParameter::class, ['name' => $foo]);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }
    
    public function testUsesParametersPosition()
    {
        $c = new Container();
        
        $foo = new Foo();
        
        $c->set(WithParameter::class)->construct(new Foo());
        
        $resolved = $c->make(WithParameter::class, [0 => $foo]);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    } 
    
    public function testWithParameters()
    {
        $c = new Container();
                
        $this->assertInstanceOf(
            WithParameters::class,
            $c->make(WithParameters::class)
        );
    }     
}