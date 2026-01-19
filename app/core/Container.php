<?php
/**
 * Container - Contenedor de dependencias simple
 * Implementa inyección de dependencias y singleton pattern
 */

namespace App\Core;

class Container
{
    private static array $bindings = [];
    private static array $instances = [];

    /**
     * Registra una clase o interfaz en el contenedor
     *
     * @param string $abstract Nombre de la clase o interfaz
     * @param callable|string|null $concrete Implementación concreta
     * @param bool $singleton Si debe ser singleton
     * @return void
     */
    public static function bind(string $abstract, callable|string|null $concrete = null, bool $singleton = false): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        self::$bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton
        ];
    }

    /**
     * Registra un singleton en el contenedor
     *
     * @param string $abstract Nombre de la clase
     * @param callable|string|null $concrete Implementación
     * @return void
     */
    public static function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        self::bind($abstract, $concrete, true);
    }

    /**
     * Resuelve una clase del contenedor
     *
     * @param string $abstract Nombre de la clase a resolver
     * @return mixed Instancia de la clase
     * @throws \ReflectionException
     */
    public static function resolve(string $abstract): mixed
    {
        // Si ya existe una instancia singleton, devolverla
        if (isset(self::$instances[$abstract])) {
            return self::$instances[$abstract];
        }

        // Obtener la implementación concreta
        $concrete = self::$bindings[$abstract]['concrete'] ?? $abstract;
        $singleton = self::$bindings[$abstract]['singleton'] ?? false;

        // Si es un callable, ejecutarlo
        if (is_callable($concrete)) {
            $instance = $concrete(new static);
        } else {
            // Resolver con reflexión
            $instance = self::build($concrete);
        }

        // Guardar como singleton si es necesario
        if ($singleton) {
            self::$instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Construye una instancia de clase con sus dependencias
     *
     * @param string $concrete Nombre de la clase
     * @return mixed Instancia construida
     * @throws \ReflectionException
     */
    private static function build(string $concrete): mixed
    {
        $reflector = new \ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("La clase {$concrete} no es instanciable");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("No se puede resolver el parámetro {$parameter->getName()}");
                }
                continue;
            }

            // Obtener el nombre del tipo
            $typeName = $type->getName();

            // Si es un tipo built-in (int, string, etc)
            if ($type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("No se puede resolver el parámetro built-in {$parameter->getName()}");
                }
                continue;
            }

            // Resolver la dependencia recursivamente
            $dependencies[] = self::resolve($typeName);
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Verifica si una clase está registrada
     *
     * @param string $abstract Nombre de la clase
     * @return bool
     */
    public static function has(string $abstract): bool
    {
        return isset(self::$bindings[$abstract]);
    }

    /**
     * Registra una instancia existente
     *
     * @param string $abstract Nombre de la clase
     * @param mixed $instance Instancia
     * @return void
     */
    public static function instance(string $abstract, mixed $instance): void
    {
        self::$instances[$abstract] = $instance;
    }
}
