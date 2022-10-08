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

namespace Tobento\Service\Container;

use Tobento\Service\Autowire\Autowire;
use Psr\Container\ContainerInterface;
use Closure;
use Throwable;

/**
 * Resolver
 */
class Resolver implements ResolverInterface
{
    /**
     * @var Autowire
     */    
    private Autowire $autowire;
    
    /**
     * Create a new Resolver.
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->autowire = new Autowire($this->container);
    }
    
    /**
     * Resolve the given identifier to a value.
     *
     * @param string $id Identifier of the entry.
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed
     */
    public function resolve(string $id, array $parameters = []): mixed
    {
        return $this->autowire->resolve($id, $parameters);
    }
    
    /**
     * Resolve the given definition.
     *
     * @param DefinitionInterface $definition
     *
     * @return mixed The value of the resolved definition.
     */
    public function resolveDefinition(DefinitionInterface $definition): mixed
    {            
        $value = $definition->getValue() ?: $definition->getId();
        
        if (!empty($definition->getParameters()))
        {                
            if (is_string($value)) {
                $value = $this->resolve($value, $definition->getParameters());
            }
        }

        // Resolve value if it is resolvable
        if (is_string($value) && $this->isResolvable($value))
        {
            $value = $this->resolve($value);
        }
        
        // Handle closure definition.
        if ($value instanceof Closure)
        {
            $value = $value($this->container);
        }
        
        // Handle method calls.
        if (is_object($value) && !empty($definition->getMethods()))
        {
            $value = $this->callMethods($value, $definition);
        }

        return $value;        
    }    
    
    /**
     * If the given identifier is resolvable.
     *
     * @param mixed $id Identifier of the entry.
     * @return bool True if resolvable, otherwise false.
     */
    public function isResolvable(mixed $id): bool
    {
        return is_string($id) ? class_exists($id) : false;
    }
    
    /**
     * Call the methods.
     *
     * @param object $object
     * @param DefinitionInterface $definition
     * @return object
     */
    protected function callMethods(object $object, DefinitionInterface $definition): object
    {        
        foreach($definition->getMethods() as $method)
        {
            [$method, $parameters] = $method;
            
            // skip if method does not exist.
            if (!method_exists($object, $method)) {
                continue;
            }
            
            $this->autowire->call([$object, $method], $parameters);
        }
        
        return $object;
    }    
}