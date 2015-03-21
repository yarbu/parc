<?php

namespace Nodebox\Parc;

class ParseException extends \Exception
{
    /** @var ParserInterface */
    protected $parser;

    /** @var int */
    protected $from;

    /**
     * @param ParserInterface $parser The failed parser
     * @param int             $from   The position in the input string
     */
    public function __construct(ParserInterface $parser, $from)
    {
        $this->parser = $parser;
        $this->from = (int)$from;

        $message = sprintf('Parser %s failed at byte %d!', $this->parser->description(), $this->from);
        $message = sprintf('Parser failed at byte %d!', $this->from);
        parent::__construct($message);
    }
}
