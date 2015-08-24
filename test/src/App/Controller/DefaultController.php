<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController
{
    public function indexAction()
    {
        return new Response('Hello Droplet');
    }
}