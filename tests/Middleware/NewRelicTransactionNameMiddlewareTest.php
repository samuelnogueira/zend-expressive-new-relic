<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Tests\Middleware;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Test\TestNewRelicAgent;
use Throwable;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;

final class NewRelicTransactionNameMiddlewareTest extends TestCase
{
    /** @var NewRelicTransactionNameMiddleware */
    private $subject;
    /** @var TestNewRelicAgent */
    private $newRelicAgent;

    /** @throws Throwable */
    public function testProcess(): void
    {
        $route = new Route('/my-path1', $this->createMock(MiddlewareInterface::class));
        $route->setName('my.path.1');

        $request = (new ServerRequest(RequestMethodInterface::METHOD_GET, '/'))
            ->withAttribute(RouteResult::class, RouteResult::fromRoute($route));

        $this->subject->process($request, $this->createMock(RequestHandlerInterface::class));
        self::assertEquals($route->getName(), $this->newRelicAgent->getTransactionName());
    }

    /** @throws Throwable */
    public function testProcessFailure(): void
    {
        $request = (new ServerRequest(RequestMethodInterface::METHOD_GET, '/my-path1'))
            ->withAttribute(RouteResult::class, RouteResult::fromRouteFailure(null));

        $this->subject->process($request, $this->createMock(RequestHandlerInterface::class));
        self::assertNull($this->newRelicAgent->getTransactionName());
    }

    protected function setUp(): void
    {
        $this->newRelicAgent = new TestNewRelicAgent();
        $this->subject       = new NewRelicTransactionNameMiddleware($this->newRelicAgent);
    }
}
