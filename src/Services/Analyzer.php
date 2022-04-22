<?php

namespace ROrier\Config\Services;

use ROrier\Config\Foundations\AbstractParsingException;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Config\Interfaces\ParserInterface;

/**
 * Class Analyzer
 */
class Analyzer implements AnalyzerInterface
{
    /** @var ParserInterface[] */
    private array $parsers = [];

    /**
     * Analyzer constructor.
     * @param array $parsers
     */
    public function __construct(array $parsers = [])
    {
        array_walk($parsers, [$this, 'addParser']);
    }

    /**
     * @param ParserInterface $parser
     */
    protected function addParser(ParserInterface $parser): void
    {
        $this->parsers[] = $parser;
    }

    /**
     * @inheritDoc
     * @throws AbstractParsingException
     */
    public function parse(&$var)
    {
        if (is_string($var)) {
            $var = $this->parseString($var);
        }

        if (is_array($var)) {
            array_walk($var, [$this, 'parse']);
        }

        return $var;
    }

    /**
     * @param string $var
     * @return mixed
     * @throws AbstractParsingException
     */
    protected function parseString(string $var)
    {
        do {
            $match = false;

            foreach ($this->parsers as $parser) {
                if ($parser->match($var)) {
                    $var = $parser->process($var);
                    $match = true;
                    break;
                }
            }
        }
        while($match && is_string($var));

        return $var;
    }
}
