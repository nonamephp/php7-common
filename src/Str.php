<?php declare(strict_types = 1);
namespace Noname;

/**
 * Class Str
 *
 * @package Noname
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
            return strcmp($a, $b) === 0;
        } else {
            return strcasecmp($a, $b) === 0;
        }
    }

    /**
     * Checks if string contains another string.
     *
     * By default this method is case-sensitive.
     *
     * @param string $string
     * @param string $search
     * @param bool $caseSensitive
     * @return bool
     */
    public static function contains(string $string, string $search, bool $caseSensitive = true): bool
    {
        if ($caseSensitive) {
            return strpos($string, $search) !== false;
        } else {
            return stripos($string, $search) !== false;
        }
    }

    /**
     * Splits a string into an array containing each character.
     *
     * @param string $string
     * @return array
     */
    public static function toArray(string $string): array
    {
        return str_split($string);
    }
}
