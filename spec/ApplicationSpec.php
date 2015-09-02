<?php

namespace spec\Framework;

use Framework\Application;
use Framework\Droplet\DropletInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApplicationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Framework\DummyApplication');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Framework\Application');
    }

    function it_implements_ApplicationInterface()
    {
        $this->shouldHaveType('Framework\ApplicationInterface');
    }

    function it_should_return_the_correct_root_dir()
    {
        $this->getRootDir()->shouldEndWith('/droplet-framework/spec');
    }

    function its_name_should_be_MyApp_by_default()
    {
        $this->getName()->shouldReturn('MyApp');
    }

    function it_can_be_constructed_with_a_name()
    {
        $this->beConstructedWith(null, 'FooApp');
        $this->getName()->shouldReturn('FooApp');
    }

    function it_can_be_constructed_with_an_environment()
    {
        $this->beConstructedWith('prod');
        $this->getEnvironment()->shouldReturn('prod');
    }

    function it_should_check_whether_it_has_an_environment_or_not()
    {
        $this->beAnInstanceOf('spec\Framework\DummyApplication', ['prod']);
        $this->isEnvironment('prod')->shouldReturn(true);
    }

    function it_should_not_suffix_the_filename_with_the_environment_when_it_is_not_defined()
    {
        $this->getConfigurationFilename()->shouldReturn('Resources/config/config.php');
    }

    function it_should_suffix_the_filename_with_the_environment_when_it_is_defined()
    {
        $this->beAnInstanceOf('spec\Framework\DummyApplication', ['prod']);
        $this->getConfigurationFilename()->shouldReturn('Resources/config/config_prod.php');
    }

    function it_should_have_no_droplets_by_default()
    {
        $this->getDroplets()->shouldHaveCount(0);
    }

    function it_can_add_a_droplet(DropletInterface $droplet)
    {
        $droplet->getName()->willReturn('droplet');
        $droplet->setApplication($this)->shouldBeCalled();
        $this->registerDroplet($droplet);
        $this->getDroplets()->shouldHaveCount(1);
    }

    function it_should_know_whether_it_was_configured_or_not()
    {
        $this->beAnInstanceOf('spec\Framework\EmptyConfigApplication');

        $this->isConfigured()->shouldReturn(false);
        $this->configure();
        $this->isConfigured()->shouldReturn(true);
    }

    function it_should_not_allow_register_two_droplets_with_same_name(
        DropletInterface $d1,
        DropletInterface $d2
    )
    {
        $d1->getName()->willReturn('droplet');
        $d1->setApplication($this)->shouldBeCalled();
        $d2->getName()->willReturn('droplet');

        $this->registerDroplet($d1);
        $this->shouldThrow(
            new \InvalidArgumentException('A droplet with name "droplet" is already registered')
        )->duringRegisterDroplet($d2);
    }

    function it_should_not_allow_to_register_a_droplet_after_application_configuration(
        DropletInterface $droplet
    )
    {
        $this->beAnInstanceOf('spec\Framework\EmptyConfigApplication');

        $this->configure();

        $this->shouldThrow(
            new \RuntimeException('You cannot register droplet to this application as it already was configured')
        )->duringRegisterDroplet($droplet);
    }

    function its_container_should_be_an_instance_of_PimpleContainer()
    {
        $container = $this->getContainer();
        $container->shouldHaveType('Pimple\Container');
    }

    function its_container_should_have_the_app_property_by_default()
    {
        $container = $this->getContainer();
        $container->offsetGet('app')->shouldReturn($this);
    }

    function its_container_should_have_the_app_root_dir_property_by_default()
    {
        $container = $this->getContainer();
        $container->offsetGet('app.root_dir')->shouldReturn(__DIR__);
    }

    function its_container_should_have_the_app_environment_property_by_default()
    {
        $this->beAnInstanceOf('spec\Framework\DummyApplication', ['prod']);
        $container = $this->getContainer();
        $container->offsetGet('app.env')->shouldReturn('prod');
    }

    function it_should_not_allow_define_unknown_droplet_configuration()
    {
        $this->shouldThrow(new \RuntimeException(
            'There is no droplet registered capable to handle the configurations for "core"'
        ))->duringConfigure();
    }

    private function configureDroplet(DropletInterface $droplet, $name = null, $dependencies = null)
    {
        if (null !== $name) {
            $droplet->getName()->willReturn($name);
        }

        if (null !== $dependencies) {
            $droplet->getDependencies()->willReturn($dependencies);
        }
    }
}

class DummyApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function getConfigurationFilename()
    {
        return sprintf('Resources/%s', parent::getConfigurationFilename());
    }
}

class EmptyConfigApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function getConfigurationFilename()
    {
        return 'Resources/config/empty_config.php';
    }
}