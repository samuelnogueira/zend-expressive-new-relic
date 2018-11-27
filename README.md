# zend-expressive-new-relic

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-new-relic/badges/build.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-new-relic/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-new-relic/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-new-relic/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-new-relic/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/zend-expressive-new-relic/?branch=master)

PSR-15 Middleware for New Relic instrumentation of PHP middleware apps (e.g. Expressive)

**Important note: because of the nature of the New Relic PHP Agent, this middleware does not work properly in async applications!** 

## Requirements

* PHP >= 7.1
* [Zend Expressive](https://docs.zendframework.com/zend-expressive/) 3 application

## Installation

This package is installable and autoloadable via Composer as [samuelnogueira/zend-expressive-new-relic](https://packagist.org/packages/samuelnogueira/zend-expressive-new-relic).

```sh
composer require samuelnogueira/zend-expressive-new-relic
```

## Example
```php
<?php // config/pipeline.php

use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;

return function (\Zend\Expressive\Application $app): void {
    // (...)
    
    // Profiling middleware 2nd most outer middleware to profile everything
    if (extension_loaded('newrelic')) {
        $app->pipe(NewRelicMiddleware::class);
    }
    
    // (...)

    // Register the routing middleware in the middleware pipeline
    $app->pipe(RouteMiddleware::class);

    // Add more middleware here that needs to introspect the routing results; this
    // might include:
    //
    // - route-based authentication
    // - route-based validation
    // - etc.
    if (extension_loaded('newrelic')) {
        $app->pipe(NewRelicTransactionNameMiddleware::class);
    }
    
    // (...)
};
```

---

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/samuelnogueira/zend-expressive-new-relic.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/samuelnogueira/zend-expressive-new-relic
