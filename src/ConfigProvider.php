<?php namespace Samuelnogueira\ZendExpressiveNewRelic;

use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddlewareFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'aliases'   => [
                    NewRelicAgentInterface::class => NewRelicAgent::class,
                ],
                'factories' => [
                    NewRelicAgent::class      => NewRelicAgentFactory::class,
                    NewRelicMiddleware::class => NewRelicMiddlewareFactory::class,
                ],
            ],
        ];
    }
}
