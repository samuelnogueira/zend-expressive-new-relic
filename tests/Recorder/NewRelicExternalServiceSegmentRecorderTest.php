<?php

declare(strict_types=1);

namespace Samuelnogueira\ZendExpressiveNewRelic\Tests\Recorder;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Samuelnogueira\ZendExpressiveNewRelic\Recorder\NewRelicExternalServiceSegmentRecorder;
use PHPUnit\Framework\TestCase;

use function usleep;

final class NewRelicExternalServiceSegmentRecorderTest extends TestCase
{
    /** @var MockObject&NewRelicAgentInterface */
    private $agent;
    /** @var NewRelicExternalServiceSegmentRecorder */
    private $subject;

    public function testRecordWithInvalidHostThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('https://example.local/');
        $this->subject->record(
            'https://example.local/',
            static function () {
                exit('This should not be called');
            },
        );
    }

    public function testRecord(): void
    {
        $this->agent
            ->expects(self::exactly(6))
            ->method('customMetric')
            ->withConsecutive(
                ['External/example.local/all', self::equalToWithDelta(110.0, 10.0)],
                ['External/all', self::equalToWithDelta(110.0, 10.0)],
                ['External/other-example.local/all', self::equalToWithDelta(210.0, 10.0)],
                ['External/all', self::equalToWithDelta(210.0, 10.0)],
                ['External/example.local/all', self::equalToWithDelta(410.0, 10.0)],
                ['External/all', self::equalToWithDelta(410.0, 10.0)],
            );

        self::assertSame(
            'I slept for 100ms',
            $this->subject->record(
                'example.local',
                static function () {
                    // Wait 100ms
                    usleep(100000);

                    return 'I slept for 100ms';
                },
            )
        );

        self::assertSame(
            200,
            $this->subject->record(
                'other-example.local',
                static function () {
                    // Wait 200ms
                    usleep(200000);

                    return 200;
                },
            )
        );

        self::assertSame(
            ['I slept for', 400],
            $this->subject->record(
                'example.local',
                static function () {
                    // Wait 400ms
                    usleep(400000);

                    return ['I slept for', 400];
                },
            )
        );
    }

    protected function setUp(): void
    {
        $this->agent   = $this->createMock(NewRelicAgentInterface::class);
        $this->subject = new NewRelicExternalServiceSegmentRecorder($this->agent);

        parent::setUp();
    }
}
