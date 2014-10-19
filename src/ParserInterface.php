<?php

namespace Nodebox\Parc;

interface ParserInterface
{
    /**
     * Returns the name of the parser.
     *
     * @return string The name.
     */
    public function name();

    /**
     * Returns a description of the parser.
     *
     * @return string The description.
     */
    public function description();

    /**
     * Accepts the given $string from position $from or throws a ParseException.
     *
     * @param string $string The string to accept
     * @param int    $from   From this position
     *
     * @return Node The node with the ParseResults
     * @throws ParseException
     */
    public function accept($string, $from);
}
