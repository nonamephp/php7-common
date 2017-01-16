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
}
