<?php

namespace Samuelnogueira\NewRelicMiddleware\Tests;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Samuelnogueira\NewRelicMiddleware\NewRelicAgentInterface;
use Samuelnogueira\NewRelicMiddleware\NewRelicMiddleware;

class NewRelicMiddlewareTest extends TestCase
{
    /** @var \Samuelnogueira\NewRelicMiddleware\NewRelicMiddleware */
    private $subject;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Samuelnogueira\NewRelicMiddleware\NewRelicAgentInterface */
    private $newRelicAgent;

    public function testProcess()
    {
        $request  = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $delegate = $this->createMock(DelegateInterface::class);

        $delegate
            ->expects(static::once())
            ->method('process')
            ->with($request)
            ->willReturn($response);
        $this->newRelicAgent
            ->expects(static::once())
            ->method('startTransaction');
        $this->newRelicAgent
            ->expects(static::once())
            ->method('endTransaction');

        $result = $this->subject->process($request, $delegate);

        static::assertSame($response, $result);
    }

    protected function setUp()
    {
        $this->newRelicAgent = $this->createMock(NewRelicAgentInterface::class);
        $this->subject       = new NewRelicMiddleware($this->newRelicAgent);
    }
}
