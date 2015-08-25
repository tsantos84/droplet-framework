<?php

namespace App\Controller;

use Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends Controller
{
    public function indexAction()
    {
        return new Response('Hello Droplet');
    }
}