<?php

namespace Nodebox\Parc\Ebnf;

class ParserGeneratorTest extends \Nodebox\Parc\TestCase
{
    public function testEbnfParser()
    {
        $ebnf = __DIR__.'/../../src/Ebnf/ebnf.ebnf';
        $ebnf = file_get_contents($ebnf);

        $code = __DIR__.'/../../src/Ebnf/EbnfParser.php';
        $code = file_get_contents($code);

        $gen = new ParserGenerator();
        $new = $gen->parser($ebnf, 'syntax', 'Nodebox\Parc\Ebnf\EbnfParser');

        $this->assertSame($code, $new);
    }
}