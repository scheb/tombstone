<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Formatter;

use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Core\Model\Vampire;

class JsonFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return json_encode([
            'arguments' => $vampire->getArguments(),
            'file' => $vampire->getFile()->getReferencePath(),
            'line' => $vampire->getLine(),
            'method' => $vampire->getMethod(),
            'stackTrace' => $this->getStackTraceValues($vampire->getStackTrace()),
            'metadata' => $vampire->getMetadata(),
            'invocationDate' => $vampire->getInvocationDate(),
            'invoker' => $vampire->getInvoker(),
        ]).PHP_EOL;
    }

    private function getStackTraceValues(StackTrace $stackTrace): array
    {
        return array_map(function (StackTraceFrame $frame): array {
            return [
                'file' => $frame->getFile()->getReferencePath(),
                'line' => $frame->getLine(),
                'method' => $frame->getMethod(),
            ];
        }, iterator_to_array($stackTrace));
    }
}
