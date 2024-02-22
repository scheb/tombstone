<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

/**
 * @extends \IteratorAggregate<array-key, StackTraceFrame>
 * @extends \ArrayAccess<array-key, StackTraceFrame>
 */
interface StackTraceInterface extends \IteratorAggregate, \ArrayAccess
{
    public function getHash(): int;
}
