<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\Test\TestNewRelicAgent;
use Throwable;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;

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
        $path    = '/my-path1';
        $request = (new ServerRequest())
            ->withUri(new Uri($path))
            ->withAttribute(RouteResult::class, RouteResult::fromRouteFailure(null));

        $this->subject->process($request, $this->createMock(RequestHandlerInterface::class));
        static::assertEquals($path, $this->newRelicAgent->getTransactionName());
    }

    protected function setUp()
    {
        $this->newRelicAgent = new TestNewRelicAgent();
        $this->subject       = new NewRelicTransactionNameMiddleware($this->newRelicAgent);
    }
}
