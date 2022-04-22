<?php

namespace ROrier\Config\Services\ConfigParsers;

use ROrier\Config\Exceptions\Parsing\ParsingConstantException;
use ROrier\Config\Interfaces\ParserInterface;

class ConstantParser implements ParserInterface
{
    const REGEX_MATCH = '/(^|[^\\\\])(?<match>\[(?<key>([a-zA-Z0-9_\\\\]+::)?[A-Z0-9_]+)\])/';
    const REGEX_REPLACE = '/(^|[^\\\\])(\[%s\])/';
    const REGEX_CLEANING = '/(\\\\)(\[[a-zA-Z0-9_]+\])/';

    /**
     * @inheritDoc
     */
    public function match(string $var): bool
    {
        return (preg_match(self::REGEX_MATCH, $var) === 1);
    }

    /**
     * @inheritDoc
     * @throws ParsingConstantException
     */
    public function process(string $var)
    {
        preg_match(self::REGEX_MATCH, $var, $matches);

        $key = $matches['key'];

        if (!defined($key)) {
            throw new ParsingConstantException("Target constant '$key' is not defined.");
        }

        $pattern = sprintf(self::REGEX_REPLACE, preg_quote($key));
        $var = preg_replace($pattern, '${1}' . constant($key), $var);
        $var = preg_replace(self::REGEX_CLEANING, '$2', $var);

        return $var;
    }
}