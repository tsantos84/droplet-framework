README
======

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

Documentation
-------------

Writing

Running Tests
----------------------

The unit tests of Droplet Framework were written with [PHPSpec][3]

    $ vendor/bin/phpspec run

[1]: http://symfony.com/
[2]: http://symfony.com/doc/current/components/routing/introduction.html
[3]: http://symfony.com/doc/current/components/templating/introduction.html
[4]: http://www.phpspec.net/en/latest/