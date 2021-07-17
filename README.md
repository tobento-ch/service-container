# Container Service

The Container Service provides a PSR-11 container with autowiring.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
    - [Simple Example](#simple-example)
- [Documentation](#documentation)
    - [PSR-11](#psr-11)
    - [Autowiring](#autowiring)
    - [Definitions](#definitions)
    - [Make](#make)
    - [Call](#call)
    - [Resolver](#resolver)
- [Credits](#credits)
___

# Getting started

Add the latest version of the container service running this command.

```
composer require tobento/service-container
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design
- Autowiring

# Documentation

## PSR-11

```php
use Tobento\Service\Container\Container;

$container = new Container();

$has = $container->has(Foo::class);

$foo = $container->get(Foo::class);
```

## Autowiring

The container resolves any dependencies by autowiring, except build-in parameters needs a [definition](#definitions) to be resolved.

On union types parameter, the first resolvable parameter gets used if not set by definiton.

## Definitions

By providing the resolved object:

```php
use Tobento\Service\Container\Container;

class Foo
{
    public function __construct(
        private string $name
    ) {} 
}

$container = new Container();

$container->set(Foo::class, new Foo('value'));

$foo = $container->get(Foo::class);
```

By defining the missing parameters:

```php
use Tobento\Service\Container\Container;

class Foo
{
    public function __construct(
        private string $name
    ) {} 
}

$container = new Container();

// By the construct method:
$container->set(Foo::class)->construct('value');

// By the with method using parameter name:
$container->set(Foo::class)->with(['name' => 'value']);

// By the with method using parameter position:
$container->set(Foo::class)->with([0 => 'value']);

$foo = $container->get(Foo::class);
```

By using a closure:

```php
use Tobento\Service\Container\Container;

class Foo
{
    public function __construct(
        private string $name
    ) {} 
}

$container = new Container();

$container->set(Foo::class, function($container) {
    return new Foo('value');
});

$foo = $container->get(Foo::class);
```

You might configure which implementation to use:

```php
$container->set(BarInterface::class, Bar::class);
```

Defining method calls: You will need only to define build-in parameters as others get autowired if you want to.

```php
use Tobento\Service\Container\Container;

class Foo
{
    public function index(Bar $bar, string $name) {} 
}

class Bar {}

$container = new Container();

$container->set(Foo::class)->callMethod('index', ['name' => 'value']);

$container->set(Foo::class)->callMethod('index', [1 => 'value']);

$foo = $container->get(Foo::class);
```

## Make

The make() method works like get() except it will resolve the entry every time it is called.

```php
use Tobento\Service\Container\Container;

class Foo
{
    public function __construct(
        private Bar $bar,
        private string $name
    ) {} 
}

class Bar {}

$container = new Container();

$foo = $container->make(Foo::class, ['name' => 'value']);
```

## Call

For more detail visit: [service-autowire#call](https://github.com/tobento-ch/service-autowire#call)

```php
class Foo
{
    public function index(Bar $bar, string $name): string
    {
        return $name;
    } 
}

class Bar {}

$container = new Container();

$name = $container->call([Foo::class, 'index'], ['name' => 'value']);
```

## Resolver

You might adjust your requirements by adding a custom resolver which implements the following interface:

```php
use Tobento\Service\Container\Container;
use Tobento\Service\Container\ResolverInterface;

$container = new Container(new CustomResolver());
```

```php
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
     * @throws ResolverException
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
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)