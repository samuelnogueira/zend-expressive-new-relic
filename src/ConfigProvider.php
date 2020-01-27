<?php

namespace Samuelnogueira\ZendExpressiveNewRelic;

use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddlewareFactory;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddlewareFactory;

class ConfigProvider
{
    /**
     * @return mixed[]
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'aliases'   => [
                    NewRelicAgentInterface::class => NewRelicAgent::class,
                ],
                'factories' => [
                    NewRelicAgent::class                     => NewRelicAgentFactory::class,
                    NewRelicMiddleware::class                => NewRelicMiddlewareFactory::class,
                    NewRelicTransactionNameMiddleware::class => NewRelicTransactionNameMiddlewareFactory::class,
                ],
            ],
        ];
    }
}
