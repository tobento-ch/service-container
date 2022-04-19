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

use Tobento\Service\Autowire\AutowireInterface;
use Tobento\Service\Autowire\Autowire;
use Tobento\Service\Autowire\AutowireException;
use Psr\Container\ContainerInterface;
use Throwable;
use Closure;

/**
 * Container
 */
class Container implements ContainerInterface, MakeInterface, CallInterface
{    
    /**
     * @var ResolverInterface
     */    
    protected ResolverInterface $resolver;

    /**
     * @var array<string, mixed> The definitions.
     */    
    protected array $definitions = [];
    
    /**
     * @var array<string, mixed> The resolved entries.
     */    
    protected array $resolved = [];
    
    /**
     * @var array<string, bool> The ids currently resolving.
     */    
    protected array $resolving = [];
    
    /**
     * @var null|AutowireInterface
     */    
    protected null|AutowireInterface $autowire = null;    
    
    /**
     * Create a new Container.
     *
     * @param null|ResolverInterface $resolver
     */
    public function __construct(
        null|ResolverInterface $resolver = null,
    ) {
        $this->resolver = $resolver ?: new Resolver($this);
        
        $this->resolved = [
            ContainerInterface::class => $this,
            MakeInterface::class => $this,
            CallInterface::class => $this,
        ];
    }
    
    /**
     * Sets the resolver.
     *
     * @param ResolverInterface $resolver
     * @return void
     */
    public function setResolver(ResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }
    
    /**
     * Gets the resolver.
     *
     * @return ResolverInterface
     */
    public function resolver(): ResolverInterface
    {
        return $this->resolver;
    }    
    
    /**
     * If an entry by its given identifier exist.
     *
     * @param string $id Identifier of the entry.
     * @return bool Returns true if exist, otherwise false.
     */
    public function has(string $id): bool
    {
        return isset($this->resolved[$id])
               || isset($this->definitions[$id])
               || $this->resolver->isResolvable($id);
    }
    
    /**
     * Gets an entry by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed The value obtained from the identifier.
     */
    public function get(string $id): mixed
    {        
        if (
            isset($this->definitions[$id])
            && $this->definitions[$id]->isPrototype()
        ) {
            return $this->resolve($id);
        }
        
        return $this->resolved[$id] ??= $this->resolve($id);
    }
    
    /**
     * Sets an entry by its given identifier.
     *
     * @param string $id Identifier of the entry.
     * @param mixed Any value.
     * @return DefinitionInterface
     */
    public function set(string $id, mixed $value = null): DefinitionInterface
    {
        if (! $value instanceof DefinitionInterface)
        {
            $value = new Definition($id, $value);
        }
        
        $this->definitions[$id] = $value;
        
        return $value;
    }

    /**
     * Makes an entry by its identifier.
     *
     * @param string $id Identifier of the entry.
     * @param array<int|string, mixed> $parameters The parameters.
     * @return mixed The value obtained from the identifier.
     */
    public function make(string $id, array $parameters = []): mixed
    {
        return $this->resolve($id, $parameters);
    }
    
    /**
     * Call the given callable.
     *
     * @param mixed $callable A callable.
     * @param array<int|string, mixed> $parameters The parameters.
     * @return mixed The called function result.
     */
    public function call(mixed $callable, array $parameters = []): mixed
    {
        try {
            return $this->autowire()->call($callable, $parameters);
        } catch (AutowireException $e) {
            throw new ContainerException($e->getMessage(), 0, $e);
        }
    }    

    /**
     * Get the autowire.
     *
     * @return AutowireInterface
     */
    protected function autowire(): AutowireInterface
    {
        if (is_null($this->autowire))
        {
            $this->autowire = new Autowire($this);
        }
        
        return $this->autowire;
    }
    
    /**
     * Resolve the given identifier to a value.
     *
     * @param string $id Identifier of the entry.
     * @param null|array<int|string, mixed> $parameters
     * @return mixed
     */
    protected function resolve(string $id, null|array $parameters = null): mixed
    {
        // prevent circular dependency error.
        if (isset($this->resolving[$id]))
        {
            throw new ContainerException(
                sprintf('Entry (%s) cannot be resolved: circular dependency detected', $id)
            );
        }
        
        $this->resolving[$id] = true;
        
        // If parameters are set, it is from make() method,
        // so we do not resolve definition.
        try {
            if (is_null($parameters)) {
                $value = $this->resolveWithDefiniton($id);
            } else {
                $value = $this->resolveByResolver($id, $parameters);
            }
        } catch (NotFoundException $t) {
            throw $t;
        } catch (Throwable $t) {
            throw new ContainerException($t->getMessage(), 0, $t);
        }

        unset($this->resolving[$id]);
        
        return $value;
    }

    /**
     * Resolve the given identifier to a value.
     *
     * @param string $id Identifier of the entry.
     * @return mixed
     */
    protected function resolveWithDefiniton(string $id): mixed
    {
        // Resolve definition if any.        
        if (! isset($this->definitions[$id]))
        {
            return $this->resolveByResolver($id);
        }

        return $this->resolver->resolveDefinition($this->definitions[$id]);     
    }
    
    /**
     * Resolve the given identifier to a value.
     *
     * @param string $id Identifier of the entry.
     * @param array<int|string, mixed> $parameters
     * @return mixed
     */
    protected function resolveByResolver(string $id, array $parameters = []): mixed
    {
        if (! $this->resolver->isResolvable($id))
        {
            throw new NotFoundException(sprintf('No entry was found for the id (%s)', $id));
        }
        
        return $this->resolver->resolve($id, $parameters);
    }
}