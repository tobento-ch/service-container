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
 * ContainerPrototypeTest tests
 */
class ContainerPrototypeTest extends TestCase
{
    public function testReturnsNewInstance()
    {
        $c = new Container();
        
        $this->assertSame($c->get('stdClass'), $c->get('stdClass'));
        
        $c->set('stdClass')->prototype();
        
        $this->assertFalse($c->get('stdClass') === $c->get('stdClass'));
    }
    
    public function testReturnsNewInstanceWithClosureDefinition()
    {
        $c = new Container();
        
        $c->set('closure', function () {
            return new Foo();
        })->prototype();
        
        $this->assertFalse($c->get('closure') === $c->get('closure'));
    }
    
    public function testReturnsNewInstanceWithClassDefinition()
    {
        $c = new Container();
        
        $c->set(Foo::class)->prototype();
        
        $this->assertFalse($c->get(Foo::class) === $c->get(Foo::class));
    }
    
    public function testReturnsNewInstanceWithInterfaceDefinition()
    {
        $c = new Container();
        
        $c->set(FooInterface::class, Foo::class)->prototype();
        
        $this->assertFalse($c->get(FooInterface::class) === $c->get(FooInterface::class));
    }    
    
    public function testOnStringHasNoImpact()
    {
        $c = new Container();
        
        $c->set('id', 'value')->prototype();
        
        $this->assertSame('value', $c->get('id'));
    }    
}