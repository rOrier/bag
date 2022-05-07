<?php

namespace ROrier\Config\Interfaces;

use ROrier\Config\Foundations\AbstractParsingException;

interface AnalyzerInterface
{
    /**
     * @param mixed $var
     * @return mixed
     * @throws AbstractParsingException
     */
    public function parse($var);
}