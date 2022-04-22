<?php

namespace ROrier\Config\Components\Bootstraps;

use Exception;
use ROrier\Config\Services\Analyzer;
use ROrier\Config\Components\Bag;
use ROrier\Config\Services\ConfigParsers\ArrayParameterParser;
use ROrier\Config\Services\DelayedProxies\ParametersProxy;
use ROrier\Config\Services\ConfigParsers\ConstantParser;
use ROrier\Config\Services\ConfigParsers\EnvParser;
use ROrier\Config\Services\ConfigParsers\StringParameterParser;
use ROrier\Config\Services\Parameters;

class ParametersBootstrap
{
    private Bag $data;

    private ?Parameters $parameters = null;

    /**
     * ParametersBootstrap constructor.
     */
    public function __construct()
    {
        $this->data = new Bag();
    }

    /**
     * @param array $data
     * @return self
     */
    public function addData(array $data): self
    {
        $this->data->merge($data);

        return $this;
    }

    /**
     * @return self
     */
    public function build(): self
    {
        $delayedParameters = new ParametersProxy();

        $parametersAnalyzer = new Analyzer([
            new ConstantParser(),
            new EnvParser(),
            new StringParameterParser($delayedParameters),
            new ArrayParameterParser($delayedParameters)
        ]);

        $this->parameters = new Parameters($this->data, $parametersAnalyzer);

        $delayedParameters->setParameters($this->parameters);

        return $this;
    }

    /**
     * @return Parameters
     * @throws Exception
     */
    public function get(): Parameters
    {
        if ($this->parameters === null) {
            throw new Exception("Service Parameters has not yet been built.");
        }

        return $this->parameters;
    }
}
