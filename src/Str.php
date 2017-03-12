<?php declare(strict_types = 1);
namespace Noname\Common;

/**
 * Class Str
 *
 * @package Noname\Common
 */
class Str
{
    /**
     * Checks if string starts with given prefix.
     *
     * By default this method is case-sensitive.
     *
     * @param string $string
     * @param string $prefix
     * @param bool $caseSensitive
     * @return bool
     */
    public static function startsWith(string $string, string $prefix, bool $caseSensitive = true): bool
    {
        if ($caseSensitive) {
            return strpos($string, $prefix) === 0;
        } else {
            return stripos($string, $prefix) === 0;
        }
    }

    /**
     * Checks if string ends with given prefix.
     *
     * By default this method is case-sensitive.
     *
     * @param string $string
     * @param string $suffix
     * @param bool $caseSensitive
     * @return bool
     */
    public static function endsWith(string $string, string $suffix, bool $caseSensitive = true): bool
    {
        $ending = strlen($string) - strlen($suffix);

        if ($caseSensitive) {
            return strrpos($string, $suffix) === $ending;
        } else {
            return strripos($string, $suffix) === $ending;
        }
    }

    /**
     * Checks if two strings equal each other.
     *
     * By default this method is case-sensitive.
     *
     * @param string $a
     * @param string $b
     * @param bool $caseSensitive
     * @return bool
     */
    public static function equals(string $a, string $b, bool $caseSensitive = true): bool
    {
        if ($caseSensitive) {
            return $a === $b;
        } else {
            return strtolower($a) === strtolower($b);
        }
    }
}
