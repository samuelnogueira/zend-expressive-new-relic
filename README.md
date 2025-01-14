# zend-expressive-new-relic

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/samuelnogueira/zend-expressive-new-relic)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)

PSR-15 Middleware for New Relic instrumentation of Mezzio apps.

:warning: **Will not work correctly in async applications (ex. Swoole Coroutine)** :warning:

## Requirements

* PHP ^8.0
* A [Mezzio](https://docs.mezzio.dev/mezzio/) application (formerly Zend Expressive) 

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
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Application;

return static function (Application $app): void {
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
