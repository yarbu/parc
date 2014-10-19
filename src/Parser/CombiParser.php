<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;
use Nodebox\Parc\ParserInterface;

abstract class CombiParser extends BaseParser
{
    /** @var array< string|ParserInterface > */
    protected $internals;

    /**
     * @param array $internals An Array of internal parsers.
     */
    public function __construct(array $internals = array())
    {
        foreach ($internals as $internal) {
            if ( !is_string($internal) && !$internal instanceof ParserInterface) {
                throw new \Exception(sprintf('%s is not a string and not a Parser!', var_export($internal, true)));
            }
        }
        $this->internals = $internals;
        parent::__construct();
    }

    /**
     * Returns a description of the internal parsers
     */
    protected function internalsDescription()
    {
        $chunks = array();
        foreach ($this->internals as $key => $internal) {
            $chunk = is_string($key) ? var_export($key, true).' => ' : '';
            if (is_string($internal)) {
                $chunk.= var_export($internal, true);
            } else {
                $chunk.= $internal->description();
            }
            $chunks[] = $chunk;
        }
        return sprintf('array(%s)', implode(', ', $chunks));
    }

    /**
     * Returns the first set of the parser
     *
     *
     */
    protected abstract function firstSet();
}
