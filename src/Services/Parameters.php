<?php

namespace ROrier\Config\Services;

use ROrier\Config\Components\Bag;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Config\Exceptions\ParametersException;
use ROrier\Config\Foundations\AbstractParsingException;
use ROrier\Config\Interfaces\ParametersInterface;

/**
 * Class Parameters
 */
class Parameters implements ParametersInterface
{
    private Bag $bag;

    private AnalyzerInterface $analyzer;

    /**
     * Parameters constructor.
     * @param Bag $bag
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(
        Bag $bag,
        AnalyzerInterface $analyzer
    ) {
        $this->analyzer = $analyzer;
        $this->bag = $bag;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->bag->toArray();
    }

    /**
     * @inheritDoc
     */
    public function get($var)
    {
        $value = $this->bag[$var];

        try {
            $value = $this->analyzer->parse($value);
        } catch(AbstractParsingException $exception) {
            throw new ParametersException("Parsing error for parameter key '$var' : " . $exception->getMessage(), 0, $exception);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getRaw($var)
    {
        return $this->bag[$var];
    }

    /**
     * @inheritDoc
     */
    public function has($var): bool
    {
        return isset($this->bag[$var]);
    }

    /**
     * @inheritDoc
     */
    public function getAnalyzer(): AnalyzerInterface
    {
        return $this->analyzer;
    }

    // ###################################################################
    // ###       ArrayAccess interface.
    // ###################################################################

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new ParametersException("Parameters are read-only.");
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new ParametersException("Parameters are read-only.");
    }

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}
