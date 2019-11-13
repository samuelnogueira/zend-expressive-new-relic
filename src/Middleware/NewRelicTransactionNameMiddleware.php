<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Zend\Expressive\Router\RouteResult;

class NewRelicTransactionNameMiddleware implements MiddlewareInterface
{
    /** @var NewRelicAgentInterface */
    private $newRelicAgent;

    public function __construct(NewRelicAgentInterface $newRelicAgent)
    {
        $this->newRelicAgent = $newRelicAgent;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->newRelicAgent->nameTransaction($this->getTransactionName($request));

        return $handler->handle($request);
    }

    private function getTransactionName(ServerRequestInterface $request): string
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        if ($routeResult instanceof RouteResult) {
            $matchedRoute = $routeResult->getMatchedRouteName();
            if (false !== $matchedRoute) {
                return $matchedRoute;
            }
        }

        return $request->getUri()->getPath();
    }
}
