<?php namespace Samuelnogueira\ZendExpressiveNewRelic\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;

class NewRelicMiddlewareTest extends TestCase
{
    /** @var NewRelicMiddleware */
    private $subject;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface */
    private $newRelicAgent;

    /**
     * @throws \Throwable
     */
    public function testProcess()
    {
        $request  = $this->createMock(ServerRequestInterface::class);
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
     * @expectedException \Error
     * @throws \Throwable
     */
    public function testErrorHandling()
    {
        $request      = $this->createMock(ServerRequestInterface::class);
        $handler      = $this->createMock(RequestHandlerInterface::class);
        $errorMessage = "Error string message with meaningful information";
        $error        = new \Error($errorMessage);

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

    protected function setUp()
    {
        $this->newRelicAgent = $this->createMock(NewRelicAgentInterface::class);
        $this->subject       = new NewRelicMiddleware($this->newRelicAgent);
    }
}
