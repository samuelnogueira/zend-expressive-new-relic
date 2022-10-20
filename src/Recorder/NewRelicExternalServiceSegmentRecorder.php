<?php

declare(strict_types=1);

namespace Samuelnogueira\ZendExpressiveNewRelic\Recorder;

use Samuelnogueira\ZendExpressiveNewRelic\Exception\InvalidArgumentException;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;

use function str_contains;

final class NewRelicExternalServiceSegmentRecorder
{
    /** @var NewRelicAgentInterface */
    private $agent;

    public function __construct(NewRelicAgentInterface $agent)
    {
        $this->agent = $agent;
    }

    /**
     * @template T
     *
     * @param string       $host The external service host name (ex. `example.com`). Must NOT contain forward slashes.
     * @param callable():T $func The function that should be timed to create the external service segment.
     *
     * @return T
     *
     * @throws InvalidArgumentException If `$host` is not valid.
     */
    public function record(string $host, callable $func)
    {
        if (str_contains($host, '/')) {
            throw new InvalidArgumentException("Host must not contain forward-slashes (`/`), '$host' given.");
        }

        $start    = microtime(true) * 1000;
        $result   = $func();
        $end      = microtime(true) * 1000;
        $duration = $end - $start;

        // Only report external service metrics if function did not fail with an exception.
        $this->agent->customMetric("External/$host/all", $duration);
        $this->agent->customMetric('External/all', $duration);

        return $result;
    }
}
