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
 * ResolverInterface
 */
interface ResolverInterface
{    
    /**
     * Resolve the given identifier to a value.
     *
     * @param string $id Identifier of the entry.
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed
     */
    public function resolve(string $id, array $parameters = []): mixed;

    /**
     * Resolve the given definition.
     *
     * @param DefinitionInterface $definition
     *
     * @return mixed The value of the resolved definition.
     */
    public function resolveDefinition(DefinitionInterface $definition): mixed;
    
    /**
     * If the given identifier is resolvable.
     *
     * @param mixed $id Identifier of the entry.
     * @return bool True if resolvable, otherwise false.
     */
    public function isResolvable(mixed $id): bool;
}