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

/**
 * Definition
 */
class Definition implements DefinitionInterface
{    
    /**
     * Create a new Definition
     *
     * @param string $id Identifier of the entry.
     * @param mixed Any value.
     * @param array<int|string, mixed> $parameters The parameters.
     * @param array<int, array<int|string, mixed>> $methods The methods to be called.
     */
    public function __construct(
        protected string $id,
        protected mixed $value = null,
        protected array $parameters = [],
        protected array $methods = []
    ) {}

    /**
     * Get the id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    /**
     * Get the value.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
    
    /**
     * Get the parameters.
     *
     * @return array<int|string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    /**
     * Get the methods.
     *
     * @return array<int, array<int|string, mixed>>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }    

    /**
     * Set the parameters.
     *
     * @param array<int|string, mixed> $parameters The parameters.
     * @return static $this
     */
    public function with(array $parameters = []): static
    {
        $this->parameters = $parameters;
        
        return $this;
    }
    
    /**
     * Set the parameters.
     *
     * @param mixed ...$parameters The parameters.
     * @return static $this
     */
    public function construct(...$parameters): static
    {
        $this->parameters = $parameters;
        
        return $this;
    }
    
    /**
     * Set a method to call with parameters.
     *
     * @param string $method The name of the method
     * @param array<int|string, mixed> $parameters The parameters.
     * @return static $this
     */
    public function callMethod(string $method, array $parameters = []): static
    {
        $this->methods[] = [$method, $parameters];
        
        return $this;
    }
}