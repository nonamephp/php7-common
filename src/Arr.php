<?php declare(strict_types = 1);
namespace Noname\Common;

/**
 * Class Arr
 *
 * @package Noname\Common
 */
class Arr
{
    /**
     * Flatten an associative array using a custom separator.
     *
     * By default this method will use a dot (.) as the separator.
     *
     * @param array $array
     * @param string $separator
     * @param string $prepend
     * @return array
     */
    public static function flatten(array $array, string $separator = '.', string $prepend = ''): array
    {
        $flatArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $flatArray += self::flatten($value, $separator, $prepend . $key . $separator);
            } else {
                $flatArray[$prepend . $key] = $value;
            }
        }

        return $flatArray;
    }

    /**
     * Flatten an associative array using a dot (.) separator.
     *
     * @param array $array
     * @return array
     */
    public static function dot(array $array): array
    {
        return self::flatten($array);
    }

    /**
     * Recursively assign the callable's return value to each array item.
     *
     * Array keys are preserved.
     *
     * @param array $array
     * @param callable $callable
     * @return array
     */
    public static function each(array $array, callable $callable): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $array[$key] = self::each($value, $callable);
            } else {
                $array[$key] = call_user_func($callable, $value);
            }
        }
        return $array;
    }
}
