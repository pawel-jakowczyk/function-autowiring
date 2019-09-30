<?php
namespace pj\autowiring;

use Psr\Container\ContainerInterface;

class FunctionParametersResolver
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(string $functionName): array
    {
        $reflectionAction = new \ReflectionFunction($functionName);
        $arguments = [];
        foreach ($reflectionAction->getParameters() as $parameter) {
            if (!$reflectionClass = $parameter->getClass()) {
                throw new \Exception('Parameter has no class type and could not be resolved by container');
            }
            $parameterClass = $reflectionClass->name;
            if (!$object = $this->container->get($parameterClass)) {
                throw new \Exception('Container does not know how to resolve parameter class: ' . $parameterClass);
            }
            $arguments[] = $this->container->get($parameterClass);
        }
        return $arguments;
    }
}