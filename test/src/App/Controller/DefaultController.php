<?php

namespace App\Controller;

use Framework\Controller\Controller;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends Controller
{

    public function indexAction($name)
    {
        return $this->render('welcome.html.php', [
            'name' => $name
        ]);
    }
}