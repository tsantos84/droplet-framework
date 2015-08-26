<?php

namespace spec\Framework\Droplet\Core;

use PhpSpec\ObjectBehavior;
use Pimple\Container;
use Prophecy\Argument;

class ParameterDropletSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Framework\Droplet\Core\ParameterDroplet');
    }

    function it_inherits_the_behavior_of_AbstractDroplet()
    {
        $this->shouldHaveType('Framework\Droplet\AbstractDroplet');
    }

    function it_builds_the_container_with_parameters(Container $container)
    {
        $config = [['param_1' => 'value_1']];
        $container->offsetSet('param_1', 'value_1')->shouldBeCalled();
        $this->buildContainer($config, $container);
    }
}
