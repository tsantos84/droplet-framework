<?php

namespace Framework\Controller;

use Framework\DependencyInjection\AbstractContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Controller
 * @package Framework\Controller
 */
abstract class Controller extends AbstractContainerAware
{
    /**
     * @param       $template
     * @param array $context
     *
     * @return string
     */
    public function renderView($template, array $context = [])
    {
        if (isset($this->container['templating'])) {
            return $this->container['templating']->render($template, $context);
        }

        throw new \RuntimeException('The droplet "templating" must be registered to render templates');
    }

    /**
     * @param               $template
     * @param array         $context
     * @param Response      $response
     *
     * @return Response
     */
    public function render($template, array $context = [], Response $response = null)
    {
        $content = $this->renderView($template, $context);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        if (!isset($this->container['session'])) {
            throw new \RuntimeException('The droplet "session" must be registered');
        }

        return $this->container['session'];
    }
}