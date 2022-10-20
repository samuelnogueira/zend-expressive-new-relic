<?php

declare(strict_types=1);

namespace Samuelnogueira\ZendExpressiveNewRelic\Exception;

use Exception;

final class InvalidArgumentException extends Exception implements ZendExpressiveNewRelicException
{
}
