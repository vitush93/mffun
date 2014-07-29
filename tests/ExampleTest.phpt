<?php

namespace Test;

use Nette,
    Tester,
    Tester\Assert;

$container = require __DIR__ . '/bootstrap.php';

class ExampleTest extends Tester\TestCase
{

    private $container;

    function __construct(Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    function setUp()
    {
        // prepare
    }

    function tearDown()
    {
        // cleanup
    }

    function testSomething()
    {
        
    }

    function testAny()
    {
        
    }

}

id(new ExampleTest($container))->run();
