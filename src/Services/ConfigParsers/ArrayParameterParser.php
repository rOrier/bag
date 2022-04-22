<?php

namespace ROrier\Config\Services\ConfigParsers;

use ROrier\Config\Exceptions\Parsing\ParsingParameterException;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Config\Interfaces\ParserInterface;

class ArrayParameterParser implements ParserInterface
{
    private ParametersInterface $parameters;

    public function __construct(ParametersInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function match(string $var): bool
    {
        return (substr($var, 0, 1) === '$');
    }

    /**
     * @inheritDoc
     */
    public function process(string $var)
    {
        $key = substr($var, 1);

        if (!isset($this->parameters[$key])) {
            throw new ParsingParameterException("Target parameters '$key' is not defined.");
        }

        $var = $this->parameters->getRaw($key);

        return $var;
    }
}
