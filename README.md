# Traffic

<img src="http://i.giphy.com/NLnIvegC9v5S.gif" alt="High five" align="right">

[![Master branch build status][ico-build]][travis]
[![PHP ~7.0][ico-engine]][lang]
[![Published version][ico-package]][package]
[![ISC Licensed][ico-license]][license]

**Traffic** is lightweight, but fast and flexible php web routing component. Routes are defined as regular expressions and are dispatched to a stack of callable handlers.

The easiest way to install Traffic is via [Composer][package].

```json
{
    "require": {
        "silawrenc/traffic": "*"
    }
}
```

## API

#### Adding routes
Routes can be added to the router in the format `($method, $pattern, ...$handlers)` as follows.
```php
$router = new Traffic;
$router->add('GET', '/foo/bar', function() {
    // do stuff
});
```

There are also convenience methods for GET and POST requests.
```php
$router->get('/foo/bar', function () {
    // show stuff
});

$router->post('/foo/bar', function () {
    // save stuff
});
```

#### Dynamic routes
You can specify both the method and path as regular expressions. Captures are passed as positional arguments to handlers.
```php
$router->add('(GET|POST)', '/user/([a-z]+)', function ($method, $username) {
    // as you were
});
```

A simpler syntax is also supported: you can use `/foo/{bar}` to capture part of a url (or method). The default is to match any characters except `/`. If you want to specify a pattern you can include it after a colon, e.g. `/foo/{bar:\d{4}}`. The wildcard `*` is supported for methods, and will match and capture any method name.
```php
$router->add('*', '/foo/{bar}/{baz:[A-Z]+}', function ($method, $bar, $baz) {
    // important stuff
});

$router->add('(?:PATCH|PUT)', '/foo/{bar}', function ($bar) {
    // using a non-capturing regex group for the method
});
```

The best way to get a handle on supported formats is to have a look at [passing](/test/lib/matches.php) and [failing](/test/lib/misses.php) test cases. Anything that is a valid regular expression is a valid syntax for either method or path.

#### Handlers
You can specify as many handlers as you like, and they will be called in the order they are specified. If any handler returns strictly false, none of the handlers after it are invoked.
```php
// return false from auth(), and render won't be invoked
$router->get('hello/world', auth(), render('landing'));

$router->get('hello/world', routeSpecificMiddleWare(), render('landing'), otherMiddleWare());
```

#### Routing
Once you've added all your routes, you can use the router for matching with:
```php
$router->route($method, $path);
```
This will match `$method` and `$path` against all of the routes you've added, and invoke the handlers of the first match. So be sure to specify your routes from most to least specific. The return value of the `route` method is `true` if a match is found, and `false` if not.


[travis]: https://travis-ci.org/silawrenc/traffic
[package]: https://packagist.org/packages/silawrenc/traffic
[lang]: http://php.net
[ico-build]: http://img.shields.io/travis/silawrenc/traffic/master.svg?style=flat
[ico-engine]: http://img.shields.io/badge/php-~7.0-8892BF.svg?style=flat
[ico-license]: http://img.shields.io/packagist/l/silawrenc/traffic.svg?style=flat
[ico-package]: http://img.shields.io/packagist/v/silawrenc/traffic.svg?style=flat
[license]: LICENSE
