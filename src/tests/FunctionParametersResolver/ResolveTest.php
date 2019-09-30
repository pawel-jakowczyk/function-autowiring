<?php
declare(strict_types=1);

namespace tests\FunctionPrameterResolver;

use PHPUnit\Framework\TestCase;
use pj\autowiring\FunctionParametersResolver;
use Psr\Container\ContainerInterface;

class ResolveTest extends TestCase
{
    public function testIfItReturnsEmptyArrayWhenThereAreNoParameters()
    {
        function test() {};
        $resolver = new FunctionParametersResolver($this->createContainer());
        $this->assertEquals([], $resolver->resolve('\tests\FunctionPrameterResolver\test'));
    }

    public function testIfItReturnsClassOfParameterType()
    {
        function test2(\StdClass $object) {};
        $resolver = new FunctionParametersResolver($this->createContainer());
        $this->assertEquals([new \StdClass()], $resolver->resolve('\tests\FunctionPrameterResolver\test2'));
    }

    public function testIfItThrowsExceptionWhenFunctionHasParameterWithNoClass()
    {
        function test3($param) {};
        $resolver = new FunctionParametersResolver($this->createContainer());
        $this->expectException(\Exception::class);
        $resolver->resolve('\tests\FunctionPrameterResolver\test3');
    }

    public function testIfItThrowsExceptionWhenFunctionHasParameterWithClassUnknownToContainer()
    {
        function test4(\StdClass $param) {};
        $resolver = new FunctionParametersResolver(new class implements ContainerInterface {
            public function has($id)
            {
                return true;
            }

            public function get($class)
            {
                return null;
            }
        });
        $this->expectException(\Exception::class);
        $resolver->resolve('\tests\FunctionPrameterResolver\test4');
    }

    public function createContainer(): ContainerInterface
    {
        return new class implements ContainerInterface {
            public function has($id)
            {
                return true;
            }

            public function get($class)
            {
                return new $class;
            }
        };
    }
}