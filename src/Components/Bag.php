<?php

namespace ROrier\Config\Components;

use ROrier\Config\Tools\CollectionTool;
use ArrayAccess;
use Exception;

/**
 * Class Bag
 */
class Bag implements ArrayAccess
{
    const REGEX_SYMLINK = '/^=(?<key>[a-z-A-Z0-9_-]+(\.[a-z-A-Z0-9_-]*)*)$/';

    private array $data;

    private ?string $separator = '.';

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * @param string|null $separator
     */
    public function setSeparator(?string $separator): void
    {
        $this->separator = $separator;
    }

    public function toArray() : array
    {
        return $this->expand($this->data);
    }

    /**
     * @param string|bool $var
     * @return array|bool|mixed|Bag|null
     */
    public function copy($var = false)
    {
        $data = $this->searchData($var, $this->data);

        return is_array($data) ? new self($this->expand($data)) : $data;
    }

    public function extract($var = false)
    {
        $data = $this->searchData($var, $this->data);

        return !empty($data) ? $this->expand($data) : null;
    }

    public function merge(array $data)
    {
        CollectionTool::merge($this->data, $data);
    }

    private function searchData($key, &$data)
    {
        if (!$key) {
            return $data;
        }

        $path = explode($this->separator, $key);
        $key = array_shift($path);
        $next = implode($this->separator, $path);

        if (is_array($data) and isset($data[$key])) {
            if ($this->isLink($data[$key])) {
                $next = $this->getLink($data[$key]) . (!empty($next) ? $this->separator . $next : null);
                return $this->searchData($next, $this->data);
            } else {
                return $this->searchData($next, $data[$key]);
            }
        } else {
            return null;
        }
    }

    private function expand($data)
    {
        if (is_array($data)) {
            $extracted = [];

            foreach ($data as $key => $val) {
                if ($this->isLink($val)) {
                    $link = $this->getLink($val);
                    $data = $this->searchData($link, $this->data);
                    $extracted[$key] = $this->expand($data);
                } elseif (is_array($val)) {
                    $extracted[$key] = $this->expand($val);
                } else {
                    $extracted[$key] = $val;
                }
            }
        } else {
            $extracted = $data;
        }

        return $extracted;
    }

    protected function isLink($val)
    {
        return (is_string($val) && preg_match(self::REGEX_SYMLINK, $val));
    }

    protected function getLink($val)
    {
        if (!$this->isLink($val)) {
            throw new Exception("'$val' is not a valid link.");
        }

        preg_match(self::REGEX_SYMLINK, $val, $matches);

        return $matches['key'];
    }

    // ###################################################################
    // ###       sous-fonctions d'accès par tableau
    // ###################################################################

    /**
     * @param mixed $var
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($var, $value)
    {
        throw new Exception("Write access forbidden.");
    }

    /**
     * @param mixed $var
     * @return bool
     */
    public function offsetExists($var)
    {
        return ($this->searchData($var, $this->data) !== null);
    }

    /**
     * @param mixed $var
     * @throws Exception
     */
    public function offsetUnset($var)
    {
        throw new Exception("Write access forbidden.");
    }

    /**
     * @param mixed $var
     * @return array|bool|mixed|null
     */
    public function offsetGet($var)
    {
        return $this->extract($var);
    }
}
