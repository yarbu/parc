<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;
use Nodebox\Parc\ParseException;

class GreedyMultiParser extends CombiParser
{
    /** @var int */
    protected $lower;

    /** @var int */
    protected $optional;

    /**
     * @param string|ParserInterface $internal The internal parser.
     * @param int                    $lower    The lower bound of occurrences.
     * @param int|null               $optional The maximal number of optional occurrences.
     */
    public function __construct($internal, $lower = 0, $optional = null)
    {
        $this->lower = (int)$lower;
        if (isset($optional)) {
            $this->optional = (int)$optional;
        }
        parent::__construct(array($internal));
    }

    /**
     * Returns the lower bound of occurrences.
     *
     * @return int The lower bound of occurrences.
     */
    public function lower()
    {
        return $this->lower;
    }

    /**
     * Returns the maximal number of optional occurrences.
     *
     * @return int The maximal number of optional occurrences.
     */
    public function optional()
    {
        return $this->optional;
    }

    /** */
    public function accept($string, $from)
    {
        $ifrom = $from;
        $result = array();
        for ($j = 0; $j < $this->lower; $j++) {
            $iresult = $this->internals[0]->accept($string, $ifrom);

            $ifrom+= $iresult->length();
            $result[] = $iresult;
        }

        for ($j = 0; $this->optional === null || $j < $this->optional; $j++) {
            try {
                $iresult = $this->internals[0]->accept($string, $ifrom);

                $ifrom+= $iresult->length();
                $result[] = $iresult;
            } catch (ParseException $oje) {
                break;
            }
        }

        return new Node($this, $from, $ifrom - $from, $result);
    }

    /** */
    public function createDescription()
    {
        $internal = $this->internals[0];
        $internalDescription = is_string($internal) ? var_export($internal, true) : $internal->description();
        return sprintf('GreedyMultiParser(%s, %d, %s)', $internalDescription, $this->lower, var_export($this->optional, true));
    }

    /** */
    protected function evalAcceptsEmpty()
    {
        return $this->lower == 0 || $this->internals[0]->acceptsEmpty;
    }

    /** */
    protected function firstSet()
    {
        return $this->internals;
    }
}
