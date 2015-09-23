README
======
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/de4ed03c-e513-4287-a0c6-decca30b5f58/mini.png)](https://insight.sensiolabs.com/projects/de4ed03c-e513-4287-a0c6-decca30b5f58)[![Build Status](https://travis-ci.org/tsantos84/droplet-framework.svg?branch=master)](https://travis-ci.org/tsantos84/droplet-framework) [![Latest Stable Version](https://poser.pugx.org/tsantos/droplet-framework/v/stable)](https://packagist.org/packages/tsantos/droplet-framework) [![Total Downloads](https://poser.pugx.org/tsantos/droplet-framework/downloads)](https://packagist.org/packages/tsantos/droplet-framework) [![Latest Unstable Version](https://poser.pugx.org/tsantos/droplet-framework/v/unstable)](https://packagist.org/packages/tsantos/droplet-framework) [![License](https://poser.pugx.org/tsantos/droplet-framework/license)](https://packagist.org/packages/tsantos/droplet-framework)

What is Droplet Framework?
--------------------------

Droplet Framework is a micro web framework to build applications using PHP language.
Its main purpose is to give developers the minimum configuration necessary to
start writing beautiful web applications.

Droplet Framework is based on very small pieces called "droplet" that
executes some lines of code and your application are done to benefit of
that great configuration.

Inspiration
-----------

Droplet Framework was inspired on [Symfony Framework][1] and uses some of its components
to perform some boring things like [Routing][2] and [Templating][3].

Requirements
------------

Droplet Framework requires PHP 5.4 and up.

Installation
------------

Use composer to install Droplet Framework

    $ composer require tsantos/droplet-framework

However, you can start using Droplet Framework easily through the ready to use [Droplet Application][5].

Documentation
-------------

Writing

Running Tests
----------------------

The unit tests of Droplet Framework were written with [PHPSpec][4] and functional tests with [Behat][6]

    $ vendor/bin/phpspec run
    $ vendor/bin/behat

[1]: http://symfony.com/
[2]: http://symfony.com/doc/current/components/routing/introduction.html
[3]: http://symfony.com/doc/current/components/templating/introduction.html
[4]: http://www.phpspec.net/en/latest/
[5]: https://github.com/tsantos84/droplet-application
[6]: http://docs.behat.org
