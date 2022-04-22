<?php

namespace ROrier\Config\Services\DelayedProxies;

use ROrier\Config\Exceptions\ParametersException;
use ROrier\Config\Interfaces\ParametersInterface;

/**
 * Class ParametersProxy
 */
class ParametersProxy implements ParametersInterface
{
    private ?ParametersInterface $parameters = null;

    /**
     * @return ParametersInterface
     * @throws ParametersException
     */
    public function getParameters(): ParametersInterface
    {
        if ($this->parameters === null) {
            throw new ParametersException("Parameters were not provided.");
        }

        return $this->parameters;
    }

    /**
     * @param ParametersInterface $parameters
     */
    public function setParameters(ParametersInterface $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function get($var)
    {
        $parameters = $this->getParameters();

        return $parameters->get($var);
    }

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    public function getRaw($var)
    {
        $parameters = $this->getParameters();

        return $parameters[$var];
    }

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    public function has($var): bool
    {
        $parameters = $this->getParameters();

        return isset($parameters[$var]);
    }

    // ###################################################################
    // ###       ArrayAccess interface.
    // ###################################################################

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    public function offsetSet($var, $value)
    {
        $parameters = $this->getParameters();

        $parameters[$var] = $value;
    }

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    public function offsetExists($var)
    {
        return $this->has($var);
    }

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    public function offsetUnset($var)
    {
        $parameters = $this->getParameters();

        unset($parameters[$var]);
    }

    /**
     * @inheritDoc
     * @throws ParametersException
     */
    public function offsetGet($var)
    {
        return $this->get($var);
    }
}
