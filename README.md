# new-relic-middleware

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status](https://scrutinizer-ci.com/g/samuelnogueira/new-relic-middleware/badges/build.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/new-relic-middleware/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samuelnogueira/new-relic-middleware/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/new-relic-middleware/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/samuelnogueira/new-relic-middleware/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samuelnogueira/new-relic-middleware/?branch=master)

PSR-15 Middleware for New Relic instrumentation of PHP middleware apps (e.g. Expressive)

## Requirements

* PHP >= 7.0
* A [PSR-15](https://github.com/http-interop/http-middleware) middleware dispatcher (e.g. [Stratigility](https://github.com/zendframework/zend-stratigility))

## Installation

This package is installable and autoloadable via Composer as [samuelnogueira/new-relic-middleware](https://packagist.org/packages/samuelnogueira/new-relic-middleware).

```sh
composer require samuelnogueira/new-relic-middleware
```

## Example
[Zend Expressive](https://docs.zendframework.com/zend-expressive/)
```php
<?php // config/pipeline.php

use Samuelnogueira\NewRelicMiddleware\NewRelicMiddleware;

/**
 * Setup middleware pipeline:
 * @var \Zend\Expressive\Application $app
 */
$app->pipe(NewRelicMiddleware::class);
// (...)
```

---

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/samuelnogueira/new-relic-middleware.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/samuelnogueira/new-relic-middleware
