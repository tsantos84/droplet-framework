<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @var \Framework\ApplicationInterface
     */
    private $application;

    /**
     * @var Response
     */
    private $response;

    /**
     * @BeforeScenario
     */
    public function reset()
    {
        $this->application = null;
        $this->response    = null;
    }

    /**
     * @Given /^I have created a droplet application$/
     */
    public function iHaveCreatedADropletApplication()
    {
        $this->application = new DummyApplication();
    }

    /**
     * @Given /^I have the following routes registered:$/
     */
    public function iHaveTheFollowingRoutesRegistered(\Behat\Gherkin\Node\TableNode $table)
    {
        $this->application->configure();
        $container = $this->application->getContainer();

        /** @var RouteCollection $routes */
        $routes = $container['routes'];

        foreach ($table->getHash() as $row) {

            $route = new \Symfony\Component\Routing\Route(
                $row['path'],
                ['_controller' => $row['controller']]
            );

            $route->setMethods($row['method']);

            $routes->add($row['name'], $route);
        }
    }

    /**
     * @When /^I send a (get|post) request to "([^"]*)"$/
     */
    public function iSendARequestTo($method, $path)
    {
        $request        = Request::create($path, $method);
        $this->response = $this->application->handle($request);
    }

    /**
     * @Then /^The response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($statusCode)
    {
        if ($statusCode != $this->response->getStatusCode()) {
            throw new \InvalidArgumentException(sprintf(
                'The response status code is %d, but %s was expected',
                $this->response->getStatusCode(),
                $statusCode
            ));
        }
    }

    /**
     * @Given /^The response should contains "([^"]*)"$/
     */
    public function theResponseShouldContains($string)
    {
        if (strpos($this->response->getContent(), $string) === false) {
            throw new \InvalidArgumentException(sprintf(
                'The text "%s" was not found on response content', $string
            ));
        }
    }
}

class DummyApplication extends \Framework\Application
{
    /**
     * @inheritDoc
     */
    public function registerDroplets()
    {
        $this->registerDroplet(new \Framework\Droplet\Core\KernelDroplet());
        $this->registerDroplet(new \Framework\Droplet\Core\Routing\RoutingDroplet());
    }

    /**
     * @return array
     */
    protected function loadConfiguration()
    {
        return [
            'routing' => [
                [
                    'providers' => []
                ]
            ]
        ];
    }
}

class DummyController
{
    public function indexAction()
    {
        return new Response('Hello Tales');
    }
}