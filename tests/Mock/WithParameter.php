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

namespace Tobento\Service\Container\Test\Mock;

class WithParameter
{
    public function __construct(
        protected Foo $name
    ) {}
    
    public function getName()
    {
        return $this->name;
    }     
}