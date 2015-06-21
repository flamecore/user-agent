FlameCore UserAgent
===================

[![Scrutinizer](http://img.shields.io/scrutinizer/g/FlameCore/UserAgent.svg)](https://scrutinizer-ci.com/g/FlameCore/UserAgent)
[![License](http://img.shields.io/packagist/l/flamecore/user-agent.svg)](http://www.flamecore.org/projects/user-agent)

This library provides simple browser detection for PHP. It uses a simple and fast algorithm to recognize major browsers.

The UserAgent package was developed for our spam-protection system [Gatekeeper](https://github.com/FlameCore/Gatekeeper).


Installation
------------

### Install via Composer

Create a file called `composer.json` in your project directory and put the following into it:

```
{
    "require": {
        "flamecore/user-agent": "1.0.*"
    }
}
```

[Install Composer](https://getcomposer.org/doc/00-intro.md#installation-nix) if you don't already have it present on your system:

    $ curl -sS https://getcomposer.org/installer | php

Use Composer to [download the vendor libraries](https://getcomposer.org/doc/00-intro.md#using-composer) and generate the vendor/autoload.php file:

    $ php composer.phar install

To make use of the API, include the vendor autoloader and use the classes:

```php
namespace Acme\MyApplication;

use FlameCore\UserAgent\UserAgent;

require 'vendor/autoload.php';
```


Requirements
------------

* You must have at least PHP version 5.4 installed on your system.


Contributors
------------

If you want to contribute, please see the [CONTRIBUTING](CONTRIBUTING.md) file first.

Thanks to the contributors:

* Christian Neff (secondtruth)
