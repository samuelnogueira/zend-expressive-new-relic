<?php namespace Samuelnogueira\ZendExpressiveNewRelic;

final class NewRelicAgent implements NewRelicAgentInterface
{
    /** @var bool */
    private $extensionLoaded;

    /**
     * NewRelicAgent constructor.
     */
    public function __construct()
    {
        $this->extensionLoaded = extension_loaded('newrelic');
    }

    /**
     * @inheritdoc
     */
    public function startTransaction($appname = null, $license = null): bool
    {
        if ($this->extensionLoaded) {
            $appname = $appname !== null ? $appname : ini_get('newrelic.appname');
            $license = $license !== null ? $license : ini_get('newrelic.license');

            return newrelic_start_transaction($appname, $license);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
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
    public function noticeError(string $string, \Throwable $exception = null): void
    {
        if ($this->extensionLoaded) {
            ($exception !== null) ? newrelic_notice_error($string, $exception) : newrelic_notice_error($string);
        }
    }
}
