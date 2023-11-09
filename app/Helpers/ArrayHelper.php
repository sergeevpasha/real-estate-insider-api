<?php

declare(strict_types=1);

namespace App\Helpers;

class ArrayHelper
{
    /**
     * Remove null values from an array recursively
     *
     * @param array $haystack
     * @return array
     */
    public static function removeNullValuesRecursively(array $haystack): array
    {
        foreach ($haystack as $key => &$value) {
            if (is_array($value)) {
                $value = self::removeNullValuesRecursively($value);

                if (empty($value)) {
                    unset($haystack[$key]);
                }
            } elseif (is_null($value)) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }
}
