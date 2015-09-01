<?php

namespace spec\Framework;

use Framework\Droplet\DropletInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApplicationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Framework\Application');
    }

    function it_implements_ApplicationInterface()
    {
        $this->shouldHaveType('Framework\ApplicationInterface');
    }
}
