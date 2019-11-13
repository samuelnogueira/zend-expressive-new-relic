<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Throwable;

final class NewRelicMiddleware implements MiddlewareInterface
{
    /** @var NewRelicAgentInterface */
    private $newRelicAgent;
    /** @var bool */
    private $captureParams;

    public function __construct(NewRelicAgentInterface $newRelicAgent, bool $captureParams)
    {
        $this->newRelicAgent = $newRelicAgent;
        $this->captureParams = $captureParams;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->newRelicAgent->startTransaction();
        $this->newRelicAgent->backgroundJob(false);
        try {
            $this->addCustomParameters($request);
            $response = $handler->handle($request);
            $this->newRelicAgent->endTransaction();

            return $response;
        } catch (Throwable $throwable) {
            $this->newRelicAgent->noticeError($throwable->getMessage(), $throwable);
            $this->newRelicAgent->endTransaction();
            throw $throwable;
        }
    }

    private function addCustomParameters(ServerRequestInterface $request): void
    {
        $this->newRelicAgent->addCustomParameter('url', $request->getUri()->getPath());
        $this->newRelicAgent->addCustomParameter('request.method', $request->getMethod());

        $this->addRequestHeadersCustomParameters($request->getHeaders());
        $this->addRequestParametersCustomParameters($request->getQueryParams());
    }

    private function addRequestHeadersCustomParameters(array $headers): void
    {
        foreach ($headers as $name => $values) {
            $this->newRelicAgent->addCustomParameter("request.headers.$name", implode(',', $values));
        }
    }

    private function addRequestParametersCustomParameters(array $queryParams): void
    {
        if ($this->captureParams) {
            foreach ($queryParams as $key => $value) {
                $this->newRelicAgent->addCustomParameter("request.parameters.$key", $this->toScalarParameter($value));
            }
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool|float|int|string Returns the given value if it is a scalar, or a string identifying the variable
     *                               type otherwise (ex. '[array]').
     */
    private function toScalarParameter($value)
    {
        if (is_scalar($value)) {
            return $value;
        } else {
            return '[' . gettype($value) . ']';
        }
    }
}
