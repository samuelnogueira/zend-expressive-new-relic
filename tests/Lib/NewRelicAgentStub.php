<?php namespace Samuelnogueira\ZendExpressiveNewRelic\Tests\Lib;

use Samuelnogueira\ZendExpressiveNewRelic\NewRelicAgentInterface;

class NewRelicAgentStub implements NewRelicAgentInterface
{
    private $customParameters = [];

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
    public function noticeError(string $string, \Throwable $exception = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function nameTransaction(string $name): bool
    {
        return false;
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

    /**
     * @return mixed[]
     */
    public function getCustomParameters(): array
    {
        return $this->customParameters;
    }
}
