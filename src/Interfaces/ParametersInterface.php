<?php

namespace ROrier\Config\Interfaces;

use ArrayAccess;
use ROrier\Config\Exceptions\ParametersException;

/**
 * Interface ParametersInterface
 */
interface ParametersInterface extends ArrayAccess
{
    /**
     * @param mixed $var
     * @return mixed
     * @throws ParametersException
     */
    public function get($var);

    /**
     * @param mixed $var
     * @return mixed
     */
    public function getRaw($var);

    /**
     * @param mixed $var
     * @return bool
     */
    public function has($var): bool;

    /**
     * @return AnalyzerInterface
     */
    public function getAnalyzer(): AnalyzerInterface;
}
