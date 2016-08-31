<?php

/**
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Generator\Resolver\Value\Chainable;

use Nelmio\Alice\Definition\Fixture\DummyFixture;
use Nelmio\Alice\Definition\Fixture\FakeFixture;
use Nelmio\Alice\Definition\Value\DummyValue;
use Nelmio\Alice\Definition\Value\FakeValue;
use Nelmio\Alice\Definition\Value\FixtureReferenceValue;
use Nelmio\Alice\Definition\Value\VariableValue;
use Nelmio\Alice\FixtureBag;
use Nelmio\Alice\Generator\FakeObjectGenerator;
use Nelmio\Alice\Generator\GenerationContext;
use Nelmio\Alice\Generator\ObjectGeneratorInterface;
use Nelmio\Alice\Generator\ResolvedFixtureSet;
use Nelmio\Alice\Generator\ResolvedFixtureSetFactory;
use Nelmio\Alice\Generator\ResolvedValueWithFixtureSet;
use Nelmio\Alice\Generator\Resolver\Value\ChainableValueResolverInterface;
use Nelmio\Alice\Generator\Resolver\Value\FakeValueResolver;
use Nelmio\Alice\Generator\ValueResolverInterface;
use Nelmio\Alice\ObjectBag;
use Nelmio\Alice\ParameterBag;
use Prophecy\Argument;

/**
 * @covers Nelmio\Alice\Generator\Resolver\Value\Chainable\VariableValueResolver
 */
class VariableValueResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAChainableResolver()
    {
        $this->assertTrue(is_a(VariableValueResolver::class, ChainableValueResolverInterface::class, true));
    }

    /**
     * @expectedException \DomainException
     */
    public function testIsNotClonable()
    {
        clone new VariableValueResolver();
    }

    public function testCanResolveVariableValues()
    {
        $resolver = new VariableValueResolver();

        $this->assertTrue($resolver->canResolve(new VariableValue('')));
        $this->assertFalse($resolver->canResolve(new FakeValue()));
    }

    public function testGetsTheVariableFromTheScope()
    {
        $value = new VariableValue('ping');
        $set = ResolvedFixtureSetFactory::create(
            new ParameterBag(['foo' => 'bar'])
        );
        $scope = ['ping' => 'pong'];

        $expected = new ResolvedValueWithFixtureSet('pong', $set);

        $resolver = new VariableValueResolver();
        $actual = $resolver->resolve($value, new FakeFixture(), $set, $scope);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Nelmio\Alice\Exception\Generator\Resolver\UnresolvableValueException
     * @expectedExceptionMessage Could not find a variable "foo".
     */
    public function testThrowsAnExceptionIfTheVariableCannotBeFoundInTheScope()
    {
        $value = new VariableValue('foo');
        $set = ResolvedFixtureSetFactory::create();

        $resolver = new VariableValueResolver();
        $resolver->resolve($value, new FakeFixture(), $set);
    }
}
