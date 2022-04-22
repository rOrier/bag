<?php

namespace ROrier\Config\Interfaces;

use ROrier\Config\Foundations\AbstractParsingException;

interface ParserInterface
{
    /**
     * @param string $var
     * @return bool
     */
    public function match(string $var) : bool;

    /**
     * @param string $var
     * @return mixed
     * @throws AbstractParsingException
     */
    public function process(string $var);
}