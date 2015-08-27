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

    function it_can_resolve_the_droplets_dependencies(
        DropletInterface $d1,
        DropletInterface $d2,
        DropletInterface $d3,
        DropletInterface $d4
    )
    {
        $d1->getName()->willReturn('d1');
        $d1->getDependencies()->willReturn([]);
        $d1->setApplication($this)->shouldBeCalled();

        $d2->getName()->willReturn('d2');
        $d2->getDependencies()->willReturn(['d1']);
        $d2->setApplication($this)->shouldBeCalled();

        $d3->getName()->willReturn('d3');
        $d3->getDependencies()->willReturn(['d2', 'd1']);
        $d3->setApplication($this)->shouldBeCalled();

        $d4->getName()->willReturn('d4');
        $d4->getDependencies()->willReturn(['d3']);
        $d4->setApplication($this)->shouldBeCalled();

        $this->registerDroplet($d1);
        $this->registerDroplet($d2);
        $this->registerDroplet($d3);
        $this->registerDroplet($d4);

        $this->resolveDroplet();
    }
}
