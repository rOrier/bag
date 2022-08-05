<?php

namespace ROrier\Config\Tools;

abstract class EnvTool
{
    /**
     * @param string $name
     * @return bool
     */
    public static function hasVar(string $name): bool
    {
        return !is_null(self::getVar($name));
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function getVar(string $name)
    {
        $value = null;

        if (isset($_ENV[$name])) {
            $value = $_ENV[$name];
        } elseif (getenv($name) !== false) {
            $value = getenv($name);
        }

        return $value;
    }
}
