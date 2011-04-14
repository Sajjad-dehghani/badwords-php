Badwords PHP
============

Badwords PHP is **small lightweight PHP library** for detecting "bad" words, e.g. profanity, in content.

Aside from the obvious matching if a word is present in a string, the filter also tries to detect words similar to those in the list, e.g. `gl@d` and `glad`.

The library is designed to be **highly configurable**, from the word lists used to the character replacement configuration at the heart of the filter.

**Note:** At present the default configuration provided is **not** a bulletproof/catch-all solution, but it will catch most variations. This will become more robust over time.

Requirements
------------

* The library is only supported on PHP 5.3.0 and up.
* It has been assumed an autoloader will be present. If you require one, you can find one [here][1].

Installation
------------

Simply download the library and add the `src` folder to your project.

Usage
-----

_Coming soon_

Testing
-------

To run the tests, make sure you have PHPUnit 3.5.0 and up installed, and just run the following in the project root,

    phpunit

[1]: http://groups.google.com/group/php-standards/web/psr-0-final-proposal