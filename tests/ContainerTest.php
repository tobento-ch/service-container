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
 * ContainerTest tests
 */
class ContainerTest extends TestCase
{
    public function testContainerIsPrs11()
    {
        $this->assertInstanceof(
            ContainerInterface::class,
            new Container()
        );
    } 
    
    public function testThrowsNotFoundExceptionIfNotFound()
    {
        $this->expectException(NotFoundException::class);
        
        (new Container())->get('bar');
    }
    
    public function testThrowsContainerExceptionIfNotResolvable()
    {
        $this->expectException(ContainerException::class);
        
        (new Container())->get(WithBuildInParameter::class);
    }    
    
    public function testWithClassName()
    {
        $c = new Container();
        
        $this->assertInstanceOf('stdClass', $c->get('stdClass'));
    }
    
    public function testResolvesEntryOnce()
    {
        $c = new Container();
        
        $this->assertSame($c->get('stdClass'), $c->get('stdClass'));
    }    

    public function testResolvesSetString()
    {
        $c = new Container();
        
        $c->set('id', 'value');
        
        $this->assertSame('value', $c->get('id'));
    }
    
    public function testResolvesSetClosure()
    {
        $c = new Container();
        
        $c->set('closure', function () {
            return 'value';
        });
        
        $this->assertSame('value', $c->get('closure'));
    }
    
    public function testResolvesSetClassName()
    {
        $c = new Container();
        
        $c->set('id', 'stdClass');
        
        $this->assertInstanceOf('stdClass', $c->get('id'));
    }
    
    public function testWithParameters()
    {
        $c = new Container();
        
        $this->assertInstanceOf(
            WithParameters::class,
            $c->get(WithParameters::class)
        );
    }
    
    public function testWithUnionParameterResolvesFirstFound()
    {
        $c = new Container();
        
        $resolved = $c->get(WithUnionParameter::class);
            
        $this->assertInstanceOf(
            Foo::class,
            $resolved->getName()
        );
    }
    
    public function testWithUnionParameterResolvesFirstFoundIfAllowsNull()
    {
        $c = new Container();
        
        $resolved = $c->get(WithUnionParameterAllowsNull::class);
            
        $this->assertInstanceOf(
            Foo::class,
            $resolved->getName()
        );
    }
    
    public function testWithUnionParameterAllowsNullAddsNullIfNotFound()
    {
        $c = new Container();
        
        $resolved = $c->get(WithUnionParameterAllowsNullNotFound::class);
        
        $this->assertSame(
            null,
            $resolved->getName()
        );
    }
    
    public function testWithUnionParameterUsesSetParameter()
    {
        $c = new Container();
        
        $foo = new Foo();
        
        $c->set(WithUnionParameterAllowsNull::class)->construct($foo);
        
        $resolved = $c->get(WithUnionParameterAllowsNull::class);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }
    
    public function testUsesSetParameter()
    {
        $c = new Container();
        
        $foo = new Foo();
        
        $c->set(WithParameter::class)->construct($foo);
        
        $resolved = $c->get(WithParameter::class);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }    
}