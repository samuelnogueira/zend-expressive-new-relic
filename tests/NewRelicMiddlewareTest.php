<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Tests;

use Error;
use Fig\Http\Message\RequestMethodInterface;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Tests\Lib\NewRelicAgentStub;
use Throwable;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class NewRelicMiddlewareTest extends TestCase
{
    /** @var NewRelicMiddleware */
    private $subject;

    /** @var MockObject&NewRelicAgentInterface */
    private $newRelicAgent;

    /**
     * @throws Throwable
     */
    public function testProcess()
    {
        $request  = new ServerRequest();
        $response = $this->createMock(ResponseInterface::class);
        $handler  = $this->createMock(RequestHandlerInterface::class);

        $handler
            ->expects(static::once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);
        $this->newRelicAgent
            ->expects(static::once())
            ->method('startTransaction');
        $this->newRelicAgent
            ->expects(static::once())
            ->method('endTransaction');

        $result = $this->subject->process($request, $handler);

        static::assertSame($response, $result);
    }

    /**
     * @expectedException Error
     * @throws Throwable
     */
    public function testErrorHandling()
    {
        $request      = new ServerRequest();
        $handler      = $this->createMock(RequestHandlerInterface::class);
        $errorMessage = "Error string message with meaningful information";
        $error        = new Error($errorMessage);

        $handler
            ->expects(static::once())
            ->method('handle')
            ->with($request)
            ->will(static::throwException($error));
        $this->newRelicAgent
            ->expects(static::once())
            ->method('startTransaction');
        $this->newRelicAgent
            ->expects(static::once())
            ->method('endTransaction');
        $this->newRelicAgent
            ->expects(static::once())
            ->method('noticeError')->with($errorMessage, $error);

        $this->subject->process($request, $handler);
    }

    /**
     * @throws Throwable
     */
    public function testCaptureParams()
    {
        $newRelicAgentStub = new NewRelicAgentStub();
        $subject           = new NewRelicMiddleware($newRelicAgentStub, true);
        $request           = (new ServerRequest())
            ->withQueryParams([
                'foo'  => 'bar',
                'list' => ['a', 'b'],
            ])
            ->withMethod(RequestMethodInterface::METHOD_GET)
            ->withUri(new Uri('http://www.example.com/qux?foo=bar'))
            ->withHeader('user-agent', 'smith');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects(static::once())
            ->method('handle')
            ->with($request)
            ->will(static::throwException(new LogicException('bla')));

        assert($request instanceof ServerRequestInterface);
        try {
            $subject->process($request, $handler);
        } catch (LogicException $e) {
            static::assertSame('bla', $e->getMessage());
        }

        $customParameters = $newRelicAgentStub->getCustomParameters();
        static::assertEquals('GET', $customParameters['request.method']);
        static::assertEquals('/qux', $customParameters['url']);
        static::assertEquals('smith', $customParameters['request.headers.user-agent']);
        static::assertEquals('bar', $customParameters['request.parameters.foo']);
        static::assertEquals('[array]', $customParameters['request.parameters.list']);
    }

    protected function setUp()
    {
        $this->newRelicAgent = $this->createMock(NewRelicAgentInterface::class);
        $this->subject       = new NewRelicMiddleware($this->newRelicAgent, false);
    }
}
