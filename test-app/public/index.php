<?php

declare(strict_types=1);

use Samuelnogueira\ZendExpressiveNewRelic\ConfigProvider;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddleware;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\Expressive\Application;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\ServiceManager\ServiceManager;
use Zend\Stratigility\Middleware\ErrorHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keep the global namespace clean.
 */
(function () {
    // config.php
    $aggregator = new ConfigAggregator([
        Zend\Expressive\ConfigProvider::class,
        Zend\Expressive\Router\ConfigProvider::class,
        Zend\Expressive\Router\FastRouteRouter\ConfigProvider::class,
        Zend\Expressive\Swoole\ConfigProvider::class,
        ConfigProvider::class,
    ]);
    $config     = $aggregator->getMergedConfig();
    $container  = new ServiceManager($config['dependencies']);
    $container->setService('config', $config);

    // routes.php
    $app = $container->get(Application::class);
    $app->get('/foo', function () {
        throw new LogicException('bar');
    });

    // pipeline.php
    $app->pipe(ErrorHandler::class);
    $app->pipe(NewRelicMiddleware::class);
    $app->pipe(RouteMiddleware::class);
    $app->pipe(NewRelicTransactionNameMiddleware::class);
    $app->pipe(DispatchMiddleware::class);

    $app->run();
})();
