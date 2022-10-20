<?php

declare(strict_types=1);

namespace Samuelnogueira\ZendExpressiveNewRelic\Test;

use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Throwable;

final class TestNewRelicAgent implements NewRelicAgentInterface
{
    /** @var mixed[] */
    private $customParameters = [];
    /** @var string|null */
    private $transactionName;

    /**
     * {@inheritdoc}
     */
    public function startTransaction($appname = null, $license = null): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function endTransaction($ignore = false): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function noticeError(string $string, Throwable $exception = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function nameTransaction(string $name): bool
    {
        $this->transactionName = $name;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function backgroundJob(bool $flag = true): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomParameter(string $key, $value): bool
    {
        $this->customParameters[$key] = $value;

        return true;
    }

    public function customMetric(string $metric_name, float $value): bool
    {
        return true;
    }

    /**
     * @return mixed[]
     */
    public function getCustomParameters(): array
    {
        return $this->customParameters;
    }

    public function getTransactionName(): ?string
    {
        return $this->transactionName;
    }
}
