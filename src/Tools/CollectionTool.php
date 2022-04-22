<?php

namespace ROrier\Bag\Tools;

/**
 * Class CollectionTool
 */
abstract class CollectionTool
{
    /**
     * @param array $array
     * @return bool
     */
    public static function isSequential(array $array) : bool
    {
        if (array() === $array) {
            return true;
        }

        return (array_keys($array) === range(0, count($array) - 1));
    }

    public static function merge(&$localData, $incomeData)
    {
        if (!is_array($localData) || !is_array($incomeData)) {
            $localData = $incomeData;
        } elseif (self::isSequential($localData) && self::isSequential($incomeData)) {
            $localData = array_merge($localData, $incomeData);
        } else {
            foreach ($incomeData as $key => $val) {
                if (substr($key, 0, 1) === '!') {
                    $key = substr($key, 1);
                    $localData[$key] = $val;
                } elseif (array_key_exists($key, $localData)) {
                    self::merge($localData[$key], $val);
                } else {
                    $localData[$key] = $val;
                }
            }
        }
    }
}
