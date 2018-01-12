<?php

namespace Samuelnogueira\NewRelicMiddleware;

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
     * If you have ended a transaction before your script terminates (perhaps due to it just having finished a task in
     * a job queue manager) and you want to start a new transaction, use this call. This will perform the same
     * operations that occur when the script was first started. Of the two arguments, only the application name is
     * mandatory. However, if you are processing tasks for multiple accounts, you may also provide a license for the
     * associated account. The license set for this API call will supersede all per-directory and global default
     * licenses configured in INI files.
     * If newrelic extension is not loaded, this method will do nothing.
     *
     * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-start-txn
     *
     * @param string|null $appname = ini_get('newrelic.appname')
     * @param string|null $license = ini_get('newrelic.license')
     *
     * @return bool TRUE if the transaction was successfully started, FALSE otherwise
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
     * Causes the current transaction to end immediately, and will ship all of the metrics gathered thus far to the
     * daemon unless the ignore parameter is set to true. In effect this call simulates what would happen when PHP
     * terminates the current transaction. This is most commonly used in command line scripts that do some form of job
     * queue processing. You would use this call at the end of processing a single job task, and begin a new
     * transaction (see below) when a new task is pulled off the queue. Normally, when you end a transaction you want
     * the metrics that have been gathered thus far to be recorded. However, there are times when you may want to end a
     * transaction without doing so. In this case use the second form of the function and set ignore to true.
     * If newrelic extension is not loaded, this method will do nothing.
     *
     * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-end-txn
     *
     * @param bool $ignore
     *
     * @return bool Returns true if the transaction was successfully ended and data was sent to the New Relic daemon.
     */
    public function endTransaction($ignore = false): bool
    {
        if ($this->extensionLoaded) {
            return newrelic_end_transaction($ignore);
        }

        return false;
    }
}
