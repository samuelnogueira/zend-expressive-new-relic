<?php namespace Samuelnogueira\ZendExpressiveNewRelic\Middleware;

use Psr\Container\ContainerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;

class NewRelicMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): NewRelicMiddleware
    {
        return new NewRelicMiddleware(
            $container->get(NewRelicAgentInterface::class)
        );
    }
}
