<?php namespace Samuelnogueira\ZendExpressiveNewRelic;

interface NewRelicAgentInterface
{
    /**
     * If you have ended a transaction before your script terminates (perhaps due to it just having finished a task in
     * a job queue manager) and you want to start a new transaction, use this call. This will perform the same
     * operations that occur when the script was first started. Of the two arguments, only the application name is
     * mandatory. However, if you are processing tasks for multiple accounts, you may also provide a license for the
     * associated account. The license set for this API call will supersede all per-directory and global default
     * licenses configured in INI files.
     * If newrelic extension is not loaded, this method will do nothing.
     * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-start-txn
     *
     * @param string|null $appname = ini_get('newrelic.appname')
     * @param string|null $license = ini_get('newrelic.license')
     *
     * @return bool TRUE if the transaction was successfully started, FALSE otherwise
     */
    public function startTransaction($appname = null, $license = null): bool;

    /**
     * Causes the current transaction to end immediately, and will ship all of the metrics gathered thus far to the
     * daemon unless the ignore parameter is set to true. In effect this call simulates what would happen when PHP
     * terminates the current transaction. This is most commonly used in command line scripts that do some form of job
     * queue processing. You would use this call at the end of processing a single job task, and begin a new
     * transaction (see below) when a new task is pulled off the queue. Normally, when you end a transaction you want
     * the metrics that have been gathered thus far to be recorded. However, there are times when you may want to end a
     * transaction without doing so. In this case use the second form of the function and set ignore to true.
     * If newrelic extension is not loaded, this method will do nothing.
     * @see https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-end-txn
     *
     * @param bool $ignore
     *
     * @return bool Returns true if the transaction was successfully ended and data was sent to the New Relic daemon.
     */
    public function endTransaction($ignore = false): bool;

    /**
     * Report an error at this line of code, with a complete stack trace.
     * Only the exception for the last call is retained during the course of a transaction.
     * Agent version 4.3 enhanced this form to use the exception class as the category for grouping within the New
     * Relic APM user interface. The exception parameter must be a valid PHP Exception class, and the stack frame
     * recorded in that class will be the one reported, rather than the stack at the time this function was called.
     * When using this form, if the error message is empty, a standard message in the same format as created by
     * Exception::__toString() will be automatically generated.
     *
     * @link https://docs.newrelic.com/docs/agents/php-agent/configuration/php-agent-api#api-notice-error
     *
     * @param string    $string
     * @param \Throwable $exception [optional]
     *
     * @return void
     */
    public function noticeError(string $string, \Throwable $exception = null): void;

    /**
     * Set custom name for current transaction.
     * Sets the name of the transaction to the specified name. This can be useful if you have implemented your own
     * dispatching scheme and want to name transactions according to their purpose. Call this function as early as
     * possible.
     * Do not use brackets [suffix] at the end of your transaction name. New Relic automatically strips brackets from
     * the name. Instead, use parentheses (suffix) or other symbols if needed. Unique values like URLs, Page Titles,
     * Hex Values, Session IDs, and uniquely identifiable values should not be used in naming your transactions.
     * Instead, add that data to the transaction as a custom parameter with the newrelic_add_custom_parameter() call.
     * Do not create more than 1000 unique transaction names (for example, avoid naming by URL if possible). This will
     * make your charts less useful, and you may run into limits New Relic sets on the number of unique transaction
     * names per account. It also can slow down the performance of your application.
     *
     * @see https://docs.newrelic.com/docs/agents/php-agent/php-agent-api/newrelic_name_transaction
     *
     * @param string $name Required. Name of the transaction.
     *
     * @return bool Returns true if the transaction name was successfully changed. If false is returned, check the
     *              agent log for more information.
     */
    public function nameTransaction(string $name): bool;
}
