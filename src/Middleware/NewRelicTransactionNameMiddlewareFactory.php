<?php namespace Samuelnogueira\ZendExpressiveNewRelic\Middleware;

use Psr\Container\ContainerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;

class NewRelicTransactionNameMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): NewRelicTransactionNameMiddleware
    {
        return new NewRelicTransactionNameMiddleware(
            $container->get(NewRelicAgentInterface::class)
        );
    }
}
