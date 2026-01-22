<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Middleware;

use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Mezzio\Router\RouteResult;

/** @final */
class NewRelicTransactionNameMiddleware implements MiddlewareInterface
{
    /** @var NewRelicAgentInterface */
    private $newRelicAgent;

    public function __construct(NewRelicAgentInterface $newRelicAgent)
    {
        $this->newRelicAgent = $newRelicAgent;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $transactionName = $this->getTransactionName($request);
        if ($transactionName !== null) {
            $this->newRelicAgent->nameTransaction($transactionName);
        }

        return $handler->handle($request);
    }

    private function getTransactionName(ServerRequestInterface $request): ?string
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        if ($routeResult instanceof RouteResult) {
            $matchedRoute = $routeResult->getMatchedRouteName();
            if (false !== $matchedRoute) {
                return $matchedRoute;
            }
        }

        return null;
    }
}
