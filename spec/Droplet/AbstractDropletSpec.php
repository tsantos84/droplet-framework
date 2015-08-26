<?php

namespace spec\Framework\Droplet;

use Framework\Application;
use Framework\Droplet\AbstractDroplet;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AbstractDropletSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Framework\Droplet\DummyDroplet');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Framework\Droplet\AbstractDroplet');
    }

    function it_implements_DropletInterface()
    {
        $this->shouldHaveType('Framework\Droplet\DropletInterface');
    }

    function its_application_is_mutable(Application $application)
    {
        $this->getApplication()->shouldReturn(null);
        $this->setApplication($application);
        $this->getApplication()->shouldReturn($application);
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('dummy_droplet');
        $this->__toString()->shouldReturn('dummy_droplet');
    }
}

class DummyDroplet extends AbstractDroplet
{
    public function getName()
    {
        return 'dummy_droplet';
    }
}