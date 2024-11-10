<?php

class Container
{
    private array $bindings = [];

    /**
     * Register a binding to the container
     * 
     * @param string $name
     * @param callable $resolver
     */
    public function bind(string $name, callable $resolver): void
    {
        $this->bindings[$name] = $resolver;
    }

    /**
     * Resolve a dependency from the container
     * 
     * @param string $name
     */
    public function resolve(string $name)
    {
        if (!isset($this->bindings[$name])) {
            throw new Exception("No binding found for {$name}");
        }

        return call_user_func($this->bindings[$name], $this);
    }
}
