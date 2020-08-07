<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

class PhpSyntaxHighlighter
{
    private const COLOR_HTML = 'html';
    private const COLOR_COMMENT = 'comment';
    private const COLOR_KEYWORD = 'keyword';
    private const COLOR_STRING = 'string';
    private const COLOR_DEFAULT = 'default';

    public static function formatToken(string $value, int $token): string
    {
        return self::formatValue($value, self::getColorForToken($token));
    }

    public static function formatString(string $value): string
    {
        return self::formatValue($value, self::COLOR_STRING);
    }

    public static function formatBracket(string $value): string
    {
        return self::formatValue($value, self::COLOR_KEYWORD);
    }

    private static function formatValue(string $value, string $color): string
    {
        return sprintf('<span class="%s">%s</span>', $color, $value);
    }

    private static function getColorForToken(int $token): string
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
            case T_START_HEREDOC:
            case T_END_HEREDOC:
                return self::COLOR_STRING;
            default:
                return self::COLOR_DEFAULT;
        }
    }
}
