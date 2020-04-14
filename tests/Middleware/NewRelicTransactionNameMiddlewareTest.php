<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Test\TestNewRelicAgent;
use Throwable;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;

class NewRelicTransactionNameMiddlewareTest extends TestCase
{
    /** @var NewRelicTransactionNameMiddleware */
    private $subject;
    /** @var TestNewRelicAgent */
    private $newRelicAgent;

    /**
     * @throws Throwable
     */
    public function testProcess()
    {
        $route = new Route('/my-path1', $this->createMock(MiddlewareInterface::class));
        $route->setName('my.path.1');

        $request = (new ServerRequest())
            ->withAttribute(RouteResult::class, RouteResult::fromRoute($route));

        $this->subject->process($request, $this->createMock(RequestHandlerInterface::class));
        static::assertEquals($route->getName(), $this->newRelicAgent->getTransactionName());
    }

    /**
     * @throws Throwable
     */
    public function testProcessFailure()
    {
        $request = (new ServerRequest())
            ->withUri(new Uri('/my-path1'))
            ->withAttribute(RouteResult::class, RouteResult::fromRouteFailure(null));

        $this->subject->process($request, $this->createMock(RequestHandlerInterface::class));
        static::assertNull($this->newRelicAgent->getTransactionName());
    }

    protected function setUp(): void
    {
        $this->newRelicAgent = new TestNewRelicAgent();
        $this->subject       = new NewRelicTransactionNameMiddleware($this->newRelicAgent);
    }
}
