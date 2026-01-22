<?php

declare(strict_types=1);

namespace Samuelnogueira\ZendExpressiveNewRelic\Test;

use Override;
use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;
use Throwable;

/** @api */
final class TestNewRelicAgent implements NewRelicAgentInterface
{
    /** @var mixed[] */
    private $customParameters = [];
    /** @var string|null */
    private $transactionName;
    /** @var list<array{string, float}> */
    private $customMetrics = [];

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function startTransaction($appname = null, $license = null): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function endTransaction($ignore = false): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function noticeError(string $string, ?Throwable $exception = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function nameTransaction(string $name): bool
    {
        $this->transactionName = $name;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function backgroundJob(bool $flag = true): void
    {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function addCustomParameter(string $key, $value): bool
    {
        $this->customParameters[$key] = $value;

        return true;
    }

    #[Override]
    public function customMetric(string $metric_name, float $value): bool
    {
        $this->customMetrics[] = [$metric_name, $value];

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

    /**
     * @return list<array{string, float}>
     */
    public function getCustomMetrics(): array
    {
        return $this->customMetrics;
    }
}
