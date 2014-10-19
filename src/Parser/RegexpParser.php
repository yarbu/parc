<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;
use Nodebox\Parc\ParseException;

class RegexpParser extends BaseParser
{
    /** @var string */
    protected $regexp;

    /**
     * @param string $regexp The regular expression to accept.
     */
    public function __construct($regexp)
    {
        $this->regexp = (string)$regexp;
        if (substr($this->regexp, 1, 1) !== '^') {
            throw new \Exception(sprintf('Regexp %s must anchor at the beginning of the string!', $this->regexp));
        }
        parent::__construct();
    }

    /**
     * Returns the regular expression to accept.
     *
     * @return string The regular expression to accept.
     */
    public function regexp()
    {
        return $this->regexp;
    }

    /** */
    public function accept($string, $from)
    {
        if (preg_match($this->regexp, substr($string, $from), $matches) !== 1) {
            throw new ParseException($this, $from);
        }

        return new Node($this, $from, strlen($matches[0]), $matches[0]);
    }

    /** */
    protected function createDescription()
    {
        return sprintf('new RegexpParser(%s)', var_export($this->regexp, true));
    }

    /** */
    protected function evalAcceptsEmpty()
    {
        return preg_match($this->regexp, '') === 1;
    }
}
