<?php namespace Samuelnogueira\ZendExpressiveNewRelic\Tests;

use Fig\Http\Message\RequestMethodInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Middleware\NewRelicMiddleware;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Tests\Lib\NewRelicAgentStub;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

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
     * @expectedException \Error
     * @throws \Throwable
     */
    public function testErrorHandling()
    {
        $request      = new ServerRequest();
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

    /**
     * @throws \Throwable
     */
    public function testCaptureParams()
    {
        $newRelicAgentStub = new NewRelicAgentStub();
        $subject           = new NewRelicMiddleware($newRelicAgentStub, true);
        $request           = (new ServerRequest)
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
            ->will(static::throwException(new \LogicException('bla')));

        assert($request instanceof ServerRequestInterface);
        try {
            $subject->process($request, $handler);
        } catch (\LogicException $e) {
            static::assertSame('bla', $e->getMessage());
        }

        $customParameters = (object) $newRelicAgentStub->getCustomParameters();
        static::assertAttributeEquals('GET', 'request.method', $customParameters);
        static::assertAttributeEquals('/qux', 'url', $customParameters);
        static::assertAttributeEquals('smith', 'request.headers.user-agent', $customParameters);
        static::assertAttributeEquals('bar', 'request.parameters.foo', $customParameters);
        static::assertAttributeEquals('[array]', 'request.parameters.list', $customParameters);
    }

    protected function setUp()
    {
        $this->newRelicAgent = $this->createMock(NewRelicAgentInterface::class);
        $this->subject       = new NewRelicMiddleware($this->newRelicAgent, false);
    }
}
