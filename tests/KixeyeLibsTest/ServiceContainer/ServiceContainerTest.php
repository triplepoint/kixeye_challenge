<?php

namespace KixeyeLibsTest\ServiceContainer;

use KixeyeLibs\ServiceContainer\ServiceContainer;

class ServiceContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testContainerCanStoreScalarValues()
    {
        $container = new ServiceContainer();

        $container['index'] = 'value';

        $this->assertEquals('value', $container['index']);
    }

    /**
     * @expectedException \Exception
     */
    public function testContainerExceptsOnNullIndexSet()
    {
        $container = new ServiceContainer();

        $container[] = 'value';
    }

    /**
     * @expectedException \Exception
     */
    public function testContainerExceptsOnNonexistentGet()
    {
        $container = new ServiceContainer();

        $value = $container['index'];
    }

    public function testContainerEvaluatesClosureOnGet()
    {
        $container = new ServiceContainer();

        $test_fixture = new \StdClass();

        $container['index'] = function () use ($test_fixture) {
            return $test_fixture;
        };

        $this->assertEquals($test_fixture, $container['index']);
    }
}
