<?php

namespace spec\Framework\Droplet\Core;

use PhpSpec\ObjectBehavior;
use Pimple\Container;
use Prophecy\Argument;

class TemplatingDropletSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Framework\Droplet\Core\TemplatingDroplet');
    }

    function it_inherits_the_behavior_of_AbstractDroplet()
    {
        $this->shouldHaveType('Framework\Droplet\AbstractDroplet');
    }

    function it_should_register_services_into_container(Container $container)
    {
        $container->offsetSet('templating.filesystem_loader', Argument::any())->shouldBeCalled();
        $container->offsetSet('templating.template_name_parser', Argument::any())->shouldBeCalled();
        $container->offsetSet('templating.engine.php', Argument::any())->shouldBeCalled();
        $container->offsetSet('templating', Argument::any())->shouldBeCalled();
        $this->buildContainer([['paths' => ['/path/to/view']]], $container);
    }

    function its_name_is_templating()
    {
        $this->getName()->shouldReturn('templating');
    }
}
