<?php

namespace KixeyeLibsTest\ServiceContainer;

use KixeyeLibs\ServiceContainer\ServiceContainer;

class ServiceContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \KixeyeLibs\ServiceContainer\ServiceContainer::offsetGet
     * @covers \KixeyeLibs\ServiceContainer\ServiceContainer::offsetSet
     *
     * @return void
     */
    public function testContainerCanStoreScalarValues()
    {
        $container = new ServiceContainer();

        $container['index'] = 'value';

        $this->assertEquals('value', $container['index']);
    }

    /**
     * @covers \KixeyeLibs\ServiceContainer\ServiceContainer::offsetSet
     * @expectedException \Exception
     *
     * @return void
     */
    public function testContainerExceptsOnNullIndexSet()
    {
        $container = new ServiceContainer();

        $container[] = 'value';
    }

    /**
     * @covers \KixeyeLibs\ServiceContainer\ServiceContainer::offsetGet
     * @expectedException \Exception
     *
     *  @return void
     */
    public function testContainerExceptsOnNonexistentGet()
    {
        $container = new ServiceContainer();

        $value = $container['index'];
    }

    /**
     * @covers \KixeyeLibs\ServiceContainer\ServiceContainer::offsetGet
     *
     * @return void
     */
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
