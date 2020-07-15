<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

/*
 * Based on code from PHP_CodeCoverage
 * https://github.com/sebastianbergmann/php-code-coverage
 *
 * Copyright (c) 2009-2015, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 */
class PhpFileFormatter
{
    private const COLOR_HTML = 'html';
    private const COLOR_COMMENT = 'comment';
    private const COLOR_KEYWORD = 'keyword';
    private const COLOR_STRING = 'string';
    private const COLOR_DEFAULT = 'default';

    public static function loadFile(string $file): array
    {
        $buffer = file_get_contents($file);
        $tokens = token_get_all($buffer);
        $result = [''];
        $i = 0;
        $fileEndsWithNewLine = "\n" === substr($buffer, -1);
        unset($buffer);

        foreach ($tokens as $token) {
            // Handle plain string tokens
            if (\is_string($token)) {
                $result[$i] .= self::formatValue($token, self::COLOR_KEYWORD);
                continue;
            }

            [$tokenType, $value] = $token;

            // Handle new lines
            if ("\n" === $value) {
                $result[++$i] = '';
                continue;
            }

            $value = self::replaceSpecialCharacters($value);
            $lines = explode("\n", $value);
            foreach ($lines as $lineNumber => $lineValue) {
                $lineValue = trim($lineValue);

                if ('' !== $lineValue) {
                    $result[$i] .= self::formatValue($lineValue, self::getColor($tokenType));
                }

                if (isset($lines[$lineNumber + 1])) {
                    $result[++$i] = '';
                }
            }
        }

        if ($fileEndsWithNewLine) {
            unset($result[\count($result) - 1]);
        }

        return $result;
    }

    private static function replaceSpecialCharacters(string $value): string
    {
        return str_replace(
            ["\t", ' '],
            ['&nbsp;&nbsp;&nbsp;&nbsp;', '&nbsp;'],
            htmlspecialchars($value, ENT_COMPAT)
        );
    }

    private static function formatValue(string $value, string $color): string
    {
        return sprintf(
            '<span class="%s">%s</span>',
            $color,
            $value
        );
    }

    private static function getColor(int $token): string
    {
        switch ($token) {
            case T_INLINE_HTML:
                return self::COLOR_HTML;
            case T_COMMENT:
            case T_DOC_COMMENT:
                return self::COLOR_COMMENT;
            case T_ABSTRACT:
            case T_ARRAY:
            case T_AS:
            case T_BREAK:
            case T_CALLABLE:
            case T_CASE:
            case T_CATCH:
            case T_CLASS:
            case T_CLONE:
            case T_CONTINUE:
            case T_DEFAULT:
            case T_ECHO:
            case T_ELSE:
            case T_ELSEIF:
            case T_EMPTY:
            case T_ENDDECLARE:
            case T_ENDFOR:
            case T_ENDFOREACH:
            case T_ENDIF:
            case T_ENDSWITCH:
            case T_ENDWHILE:
            case T_EXIT:
            case T_EXTENDS:
            case T_FINAL:
            case T_FINALLY:
            case T_FOREACH:
            case T_FUNCTION:
            case T_GLOBAL:
            case T_IF:
            case T_IMPLEMENTS:
            case T_INCLUDE:
            case T_INCLUDE_ONCE:
            case T_INSTANCEOF:
            case T_INSTEADOF:
            case T_INTERFACE:
            case T_ISSET:
            case T_LOGICAL_AND:
            case T_LOGICAL_OR:
            case T_LOGICAL_XOR:
            case T_NAMESPACE:
            case T_NEW:
            case T_PRIVATE:
            case T_PROTECTED:
            case T_PUBLIC:
            case T_REQUIRE:
            case T_REQUIRE_ONCE:
            case T_RETURN:
            case T_STATIC:
            case T_THROW:
            case T_TRAIT:
            case T_TRY:
            case T_UNSET:
            case T_USE:
            case T_VAR:
            case T_WHILE:
            case T_YIELD:
                return self::COLOR_KEYWORD;
            case T_CONSTANT_ENCAPSED_STRING:
                return self::COLOR_STRING;
            default:
                return self::COLOR_DEFAULT;
        }
    }
}
