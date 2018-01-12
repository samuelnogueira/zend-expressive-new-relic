<?php

namespace Samuelnogueira\NewRelicMiddleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NewRelicMiddleware implements MiddlewareInterface
{
    /** @var NewRelicAgentInterface */
    private $newRelicAgent;

    /**
     * NewRelicMiddleware constructor.
     *
     * @param NewRelicAgentInterface $newRelicAgent
     */
    public function __construct(NewRelicAgentInterface $newRelicAgent = null)
    {
        $this->newRelicAgent = $newRelicAgent ?: new NewRelicAgent();
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $this->newRelicAgent->startTransaction();
        $response = $delegate->process($request);
        $this->newRelicAgent->endTransaction();

        return $response;
    }
}
