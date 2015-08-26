<?php

namespace spec\Framework\Controller;

use Framework\DependencyInjection\AbstractContainerAware;
use PhpSpec\ObjectBehavior;
use Pimple\Container;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolverSpec extends ObjectBehavior
{
    function let(Container $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Framework\Controller\ControllerResolver');
    }

    function it_implements_ControllerResolverInterface()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\Controller\ControllerResolver');
    }

    function it_should_resolve_the_controller_from_the_service_container(
        Container $container,
        Request $request,
        ParameterBag $attributes,
        TestController $controller)
    {
        $callable = [$controller, 'indexAction'];

        $attributes->get('_controller')->willReturn('@test_controller::indexAction');
        $attributes->set('_controller', $callable)->shouldBeCalled();
        $request->attributes = $attributes;

        $container->offsetExists('test_controller')->willReturn(true);
        $container->offsetGet('test_controller')->willReturn($controller);

        /**
         * This exception is thrown when the original symfony resolver can't resolve
         * a controller by its FQN class name. In this test case, it happens because
         * symfony tries to get the controller name from the request attribute which
         * in turn returns '@test_controller' instead of the service loaded by the
         * container.
         */
        $this->shouldThrow(
            new \InvalidArgumentException('Class "@test_controller" does not exist.')
        )->duringGetController($request);
    }

    function it_should_not_resolve_an_non_existing_controller_from_service_container(
        Container $container,
        Request $request,
        ParameterBag $attributes
    )
    {
        $attributes->get('_controller')->willReturn('@test_controller::indexAction');
        $request->attributes = $attributes;
        $attributes->get('_route')->willReturn('some-route');

        $container->offsetExists('test_controller')->willReturn(false);

        $this->shouldThrow(
            new \InvalidArgumentException(
                'The controller for route some-route was defined as a service ' .
                'but the service test_controller was not found'
            )
        )->duringGetController($request);
    }
}

class TestController extends AbstractContainerAware
{
    public function indexAction()
    {
    }
}