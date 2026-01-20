<?php

/** @noinspection PhpComposerExtensionStubsInspection */

namespace Samuelnogueira\ZendExpressiveNewRelic;

use Override;
use Throwable;

use function newrelic_add_custom_parameter;
use function newrelic_background_job;
use function newrelic_custom_metric;
use function newrelic_end_transaction;
use function newrelic_name_transaction;
use function newrelic_notice_error;
use function newrelic_start_transaction;

final class NewRelicAgent implements NewRelicAgentInterface
{
    /** @var bool */
    private $extensionLoaded;

    public function __construct()
    {
        $this->extensionLoaded = extension_loaded('newrelic');
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function startTransaction($appname = null, $license = null): bool
    {
        if ($this->extensionLoaded) {
            $appname = $appname !== null ? $appname : (string) ini_get('newrelic.appname');
            $license = $license !== null ? $license : (string) ini_get('newrelic.license');

            return newrelic_start_transaction($appname, $license);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function endTransaction($ignore = false): bool
    {
        if ($this->extensionLoaded) {
            return newrelic_end_transaction($ignore);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function noticeError(string $string, ?Throwable $exception = null): void
    {
        if ($this->extensionLoaded) {
            ($exception !== null) ? newrelic_notice_error($string, $exception) : newrelic_notice_error($string);
        }
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function nameTransaction(string $name): bool
    {
        if ($this->extensionLoaded) {
            newrelic_name_transaction($name);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function backgroundJob(bool $flag = true): void
    {
        if ($this->extensionLoaded) {
            newrelic_background_job($flag);
        }
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function addCustomParameter(string $key, $value): bool
    {
        if ($this->extensionLoaded) {
            return newrelic_add_custom_parameter($key, $value);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function customMetric(string $metric_name, float $value): bool
    {
        if ($this->extensionLoaded) {
            return newrelic_custom_metric($metric_name, $value);
        }

        return false;
    }
}
