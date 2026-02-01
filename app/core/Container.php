<?php

namespace App\Core;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Exception;

class Container
{
    private const ERROR_NOT_INSTANTIABLE = 'La clase %s no es instanciable';
    private const ERROR_CANNOT_RESOLVE = 'No se puede resolver el parámetro %s';
    private const ERROR_BUILTIN_PARAM = 'No se puede resolver el parámetro built-in %s';

    private static array $bindings = [];
    private static array $instances = [];

    public static function bind(string $abstract, callable|string|null $concrete = null, bool $singleton = false): void
    {
        self::$bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'singleton' => $singleton
        ];
    }

    public static function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        self::bind($abstract, $concrete, true);
    }

    public static function resolve(string $abstract): mixed
    {
        if (self::hasSingletonInstance($abstract)) {
            return self::$instances[$abstract];
        }

        $binding = self::getBinding($abstract);
        $instance = self::createInstance($binding['concrete']);

        if ($binding['singleton']) {
            self::storeSingletonInstance($abstract, $instance);
        }

        return $instance;
    }

    public static function has(string $abstract): bool
    {
        return isset(self::$bindings[$abstract]);
    }

    public static function instance(string $abstract, mixed $instance): void
    {
        self::$instances[$abstract] = $instance;
    }

    private static function hasSingletonInstance(string $abstract): bool
    {
        return isset(self::$instances[$abstract]);
    }

    private static function getBinding(string $abstract): array
    {
        return [
            'concrete' => self::$bindings[$abstract]['concrete'] ?? $abstract,
            'singleton' => self::$bindings[$abstract]['singleton'] ?? false
        ];
    }

    private static function createInstance(callable|string $concrete): mixed
    {
        if (is_callable($concrete)) {
            return $concrete(new static);
        }

        return self::build($concrete);
    }

    private static function storeSingletonInstance(string $abstract, mixed $instance): void
    {
        self::$instances[$abstract] = $instance;
    }

    private static function build(string $concrete): mixed
    {
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception(sprintf(self::ERROR_NOT_INSTANTIABLE, $concrete));
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $concrete;
        }

        $dependencies = self::resolveDependencies($constructor->getParameters());

        return $reflector->newInstanceArgs($dependencies);
    }

    private static function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependencies[] = self::resolveDependency($parameter);
        }

        return $dependencies;
    }

    private static function resolveDependency(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type === null) {
            return self::resolveWithoutType($parameter);
        }

        if (!($type instanceof \ReflectionNamedType)) {
            throw new Exception(sprintf(self::ERROR_CANNOT_RESOLVE, $parameter->getName()));
        }

        if ($type->isBuiltin()) {
            return self::resolveBuiltinType($parameter);
        }

        return self::resolve($type->getName());
    }

    private static function resolveWithoutType(ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception(sprintf(self::ERROR_CANNOT_RESOLVE, $parameter->getName()));
    }

    private static function resolveBuiltinType(ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception(sprintf(self::ERROR_BUILTIN_PARAM, $parameter->getName()));
    }
}
