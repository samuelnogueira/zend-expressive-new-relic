<?php

declare(strict_types=1);

namespace Samuelnogueira\ZendExpressiveNewRelic\Exception;

use InvalidArgumentException as NativeInvalidArgumentException;

final class InvalidArgumentException extends NativeInvalidArgumentException implements ZendExpressiveNewRelicException
{
}
