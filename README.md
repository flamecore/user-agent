FlameCore UserAgent
===================

[![Build Status](https://img.shields.io/travis/FlameCore/UserAgent.svg)](https://travis-ci.org/FlameCore/UserAgent)
[![Scrutinizer](http://img.shields.io/scrutinizer/g/FlameCore/UserAgent.svg)](https://scrutinizer-ci.com/g/FlameCore/UserAgent)
[![Coverage](http://img.shields.io/scrutinizer/coverage/g/FlameCore/UserAgent.svg)](https://scrutinizer-ci.com/g/FlameCore/UserAgent)
[![License](http://img.shields.io/packagist/l/flamecore/user-agent.svg)](http://www.flamecore.org/projects/user-agent)

This library provides simple browser detection for PHP. It uses a simple and fast algorithm to recognize major browsers.

The UserAgent package was developed for our spam-protection system [Gatekeeper](https://github.com/FlameCore/Gatekeeper).


Why you should use it
---------------------

PHP provides a native function to detect the user browser: [`get_browser()`](http://php.net/get_browser). This function requires
the `browscap.ini` file which is 300KB+ in size. Loading and processing this file impacts the script performance. And sometimes,
the production server just doesn't provide `browscap.ini`.

Although `get_browser()` surely provides excellent detection results, in most cases a much simpler method can be just as effective.
The FlameCore UserAgent library has the advantage of being compact and easy to extend.


Usage
-----

To make use of the API, include the vendor autoloader and use the classes:

```php
namespace Acme\MyApplication;

use FlameCore\UserAgent\UserAgent;

require 'vendor/autoload.php';

// Create a user agent object
$userAgent = new UserAgent();
```

Then the parsed values can be retrieved using the getter methods:

```php
$userAgent->getBrowserName();      // firefox
$userAgent->getBrowserVersion();   // 3.6
$userAgent->getBrowserEngine();    // gecko
$userAgent->getOperatingSystem();  // linux
```

When you create a `UserAgent` object, the current user agent string is used. You can specify another user agent string:

``` php
// Use another User Agent string
$userAgent = new UserAgent('msnbot/2.0b (+http://search.msn.com/msnbot.htm)');
$userAgent->getBrowserName(); // msnbot

// Use current User Agent string
$userAgent = new UserAgent($_SERVER['HTTP_USER_AGENT']);
// ... which is equivalent to:
$userAgent = new UserAgent();
```


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


Requirements
------------

* You must have at least PHP version 5.4 installed on your system.


Contributors
------------

If you want to contribute, please see the [CONTRIBUTING](CONTRIBUTING.md) file first.

Thanks to the contributors:

* Christian Neff (secondtruth)
