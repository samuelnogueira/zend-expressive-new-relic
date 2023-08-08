<?php

namespace Samuelnogueira\ZendExpressiveNewRelic\Tests;

use Error;
use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Psr7\ServerRequest;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Test\TestNewRelicAgent;
use Throwable;

final class NewRelicMiddlewareTest extends TestCase
{
    /** @var NewRelicMiddleware */
    private $subject;

    /** @var MockObject&NewRelicAgentInterface */
    private $newRelicAgent;

    /** @throws Throwable */
    public function testProcess(): void
    {
        $request  = new ServerRequest(RequestMethodInterface::METHOD_GET, '/');
        $response = $this->createMock(ResponseInterface::class);
        $handler  = $this->createMock(RequestHandlerInterface::class);

        $handler
            ->expects(self::once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);
        $this->newRelicAgent
            ->expects(self::once())
            ->method('startTransaction');
        $this->newRelicAgent
            ->expects(self::once())
            ->method('endTransaction');

        $result = $this->subject->process($request, $handler);

        self::assertSame($response, $result);
    }

    /** @throws Throwable */
    public function testErrorHandling(): void
    {
        $request      = new ServerRequest(RequestMethodInterface::METHOD_GET, '/');
        $handler      = $this->createMock(RequestHandlerInterface::class);
        $errorMessage = "Error string message with meaningful information";
        $error        = new Error($errorMessage);

        $handler
            ->expects(self::once())
            ->method('handle')
            ->with($request)
            ->will(self::throwException($error));
        $this->newRelicAgent
            ->expects(self::once())
            ->method('startTransaction');
        $this->newRelicAgent
            ->expects(self::once())
            ->method('endTransaction');
        $this->newRelicAgent
            ->expects(self::once())
            ->method('noticeError')->with($errorMessage, $error);

        $this->expectException(Error::class);
        $this->subject->process($request, $handler);
    }

    /** @throws Throwable */
    public function testCaptureParams(): void
    {
        $newRelicAgentStub = new TestNewRelicAgent();
        $subject           = new NewRelicMiddleware($newRelicAgentStub, true);
        $request           = (new ServerRequest(
            RequestMethodInterface::METHOD_GET,
            'https://www.example.com/qux?foo=bar',
        ))
            ->withQueryParams([
                'foo'  => 'bar',
                'list' => ['a', 'b'],
            ])
            ->withHeader('user-agent', 'smith');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects(self::once())
            ->method('handle')
            ->with($request)
            ->will(self::throwException(new LogicException('bla')));

        assert($request instanceof ServerRequestInterface);
        try {
            $subject->process($request, $handler);
        } catch (LogicException $e) {
            self::assertSame('bla', $e->getMessage());
        }

        $customParameters = $newRelicAgentStub->getCustomParameters();
        self::assertEquals('GET', $customParameters['request.method']);
        self::assertEquals('/qux', $customParameters['url']);
        self::assertEquals('smith', $customParameters['request.headers.user-agent']);
        self::assertEquals('bar', $customParameters['request.parameters.foo']);
        self::assertEquals('[array]', $customParameters['request.parameters.list']);
    }

    protected function setUp(): void
    {
        $this->newRelicAgent = $this->createMock(NewRelicAgentInterface::class);
        $this->subject       = new NewRelicMiddleware($this->newRelicAgent, false);
    }
}
