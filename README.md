# phpcsrf

By [Joe Fallon](http://blog.joefallon.net)

A simple library for cross-site request forgery (CSRF) prevention in PHP. It has the 
following features:

*   Full suite of unit tests.
*   It can be integrated into any existing project.
*   Can be fully understood in just a few moments.

## Installation

The easiest way to install PhpCsrf is with
[Composer](https://getcomposer.org/). Create the following `composer.json` file
and run the `php composer.phar install` command to install it.

```json
{
    "require": {
        "joefallon/phpcsrf": "*"
    }
}
```

## Usage

### Create a Form Token

Create the form token.

```php
$sess = new Session();
$csrf = new CsrfGuard('form-name', $sess);
$csrf->generateToken();
```

Then, store the form token in the form.

```html
<input type="hidden" name="csrf" value="55517f7944ee117160414b601a15e60e1076f5b4">
```

### Validate a Form Token

```php
$sess = new Session();
$csrf = new CsrfGuard('form-name', $sess);
$csrf->isValidToken('55517f7944ee117160414b601a15e60e1076f5b4');
```
