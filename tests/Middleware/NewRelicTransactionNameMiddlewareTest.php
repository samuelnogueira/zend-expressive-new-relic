<?php namespace Samuelnogueira\ZendExpressiveNewRelic\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicTransactionNameMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;

class NewRelicTransactionNameMiddlewareTest extends TestCase
{
    /** @var NewRelicTransactionNameMiddleware */
    private $subject;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface */
    private $newRelicAgent;

    /**
     * @throws \Throwable
     */
    public function testProcess()
    {
        $route = new Route('/my-path1', $this->createMock(MiddlewareInterface::class));
        $route->setName('my.path.1');

        $request = (new ServerRequest)
            ->withAttribute(RouteResult::class, RouteResult::fromRoute($route));

        $this->newRelicAgent
            ->expects(static::once())
            ->method('nameTransaction')
            ->with($route->getName());

        $this->subject->process($request, $this->createMock(RequestHandlerInterface::class));
    }

    /**
     * @throws \Throwable
     */
    public function testProcessFailure()
    {
        $request = (new ServerRequest)
            ->withUri(new Uri('/my-path1'))
            ->withAttribute(RouteResult::class, RouteResult::fromRouteFailure(null));

        $this->newRelicAgent
            ->expects(static::once())
            ->method('nameTransaction')
            ->with($request->getUri()->getPath());

        $this->subject->process($request, $this->createMock(RequestHandlerInterface::class));
    }

    protected function setUp()
    {
        $this->newRelicAgent = $this->createMock(NewRelicAgentInterface::class);
        $this->subject       = new NewRelicTransactionNameMiddleware($this->newRelicAgent);
    }
}
