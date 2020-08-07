<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

/*
 * Based on code from PHP_CodeCoverage
 * https://github.com/sebastianbergmann/php-code-coverage
 *
 * Copyright (c) 2009-2020, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 */
class PhpFileFormatter
{
    private const HTMLSPECIALCHARS_FLAGS = ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE;

    /**
     * @var PhpSyntaxHighlighter
     */
    private $highlighter;

    public function __construct(PhpSyntaxHighlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    public function formatFile(string $file): array
    {
        $buffer = file_get_contents($file);
        $tokens = token_get_all($buffer);
        $fileEndsWithNewLine = "\n" === substr($buffer, -1);
        unset($buffer);

        $formattedLines = iterator_to_array($this->formatTokens($tokens));

        if ($fileEndsWithNewLine) {
            unset($formattedLines[\count($formattedLines) - 1]);
        }

        return $formattedLines;
    }

    /**
     * @param list<array{0: int, 1: string, 2: int}|string> $tokens
     */
    private function formatTokens(array $tokens): \Traversable
    {
        $stringFlag = false;
        $result = '';
        foreach ($tokens as $j => $token) {
            if (\is_string($token)) {
                if ('"' === $token && '\\' !== $tokens[$j - 1]) {
                    $result .= $this->highlighter->formatString($token);
                    $stringFlag = !$stringFlag;
                } else {
                    $result .= $this->highlighter->formatBracket($token);
                }
                continue;
            }

            [$token, $value] = $token;
            $value = $this->replaceSpecialCharacters($value);

            if ("\n" === $value) {
                yield $result;
                $result = '';
            } else {
                $lines = explode("\n", $value);

                if (T_START_HEREDOC === $token) {
                    $stringFlag = true;
                } elseif (T_END_HEREDOC === $token) {
                    $stringFlag = false;
                }

                foreach ($lines as $jj => $line) {
                    $line = trim($line);

                    if ('' !== $line) {
                        if ($stringFlag) {
                            $result .= $this->highlighter->formatString($line);
                        } else {
                            $result .= $this->highlighter->formatToken($line, $token);
                        }
                    }

                    if (isset($lines[$jj + 1])) {
                        yield $result;
                        $result = '';
                    }
                }
            }
        }

        yield $result;
    }

    private function replaceSpecialCharacters(string $value): string
    {
        return str_replace(
            ["\t", ' '],
            ['&nbsp;&nbsp;&nbsp;&nbsp;', '&nbsp;'],
            htmlspecialchars($value, self::HTMLSPECIALCHARS_FLAGS)
        );
    }
}
