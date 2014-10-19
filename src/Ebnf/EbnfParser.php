<?php

namespace Nodebox\Parc\Ebnf;

class EbnfParser extends \Nodebox\Parc\Parser\GrammerParser
{
    public function __construct()
    {
        $s = 'syntax';
        $internals = array(
            'syntax' => new \Nodebox\Parc\Parser\ConcatParser(array(
                'space',
                'rules',
            )),
            'rules' => new \Nodebox\Parc\Parser\GreedyMultiParser(
                'rule', 0, null
            ),
            'rule' => new \Nodebox\Parc\Parser\ConcatParser(array(
                'bareword',
                'space',
                new \Nodebox\Parc\Parser\StringParser("="),
                'space',
                'alt',
                new \Nodebox\Parc\Parser\StringParser(";"),
                'space',
            )),
            'alt' => new \Nodebox\Parc\Parser\ConcatParser(array(
                'conc',
                'pipeconclist',
            )),
            'pipeconclist' => new \Nodebox\Parc\Parser\GreedyMultiParser(
                'pipeconc', 0, null
            ),
            'pipeconc' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\StringParser("|"),
                'space',
                'conc',
            )),
            'conc' => new \Nodebox\Parc\Parser\ConcatParser(array(
                'term',
                'commatermlist',
            )),
            'commatermlist' => new \Nodebox\Parc\Parser\GreedyMultiParser(
                'commaterm', 0, null
            ),
            'commaterm' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\StringParser(","),
                'space',
                'term',
            )),
            'term' => new \Nodebox\Parc\Parser\LazyAltParser(array(
                'bareword',
                'sq',
                'dq',
                'regex',
                'group',
                'repetition',
                'optional',
            )),
            'bareword' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\RegexpParser('/^([a-z][a-z ]*[a-z]|[a-z])/'),
                'space',
            )),
            'sq' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\RegexpParser('/^\'([^\']*)\'/'),
                'space',
            )),
            'dq' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\RegexpParser('/^"([^"]*)"/'),
                'space',
            )),
            'regex' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\RegexpParser('/^\\/\\^([^\\/\\\\]*(\\\\\\/|\\\\[^\\/])?)*\\//'),
                'space',
            )),
            'group' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\StringParser("("),
                'space',
                'alt',
                new \Nodebox\Parc\Parser\StringParser(")"),
                'space',
            )),
            'repetition' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\StringParser("{"),
                'space',
                'alt',
                new \Nodebox\Parc\Parser\StringParser("}"),
                'space',
            )),
            'optional' => new \Nodebox\Parc\Parser\ConcatParser(array(
                new \Nodebox\Parc\Parser\StringParser("["),
                'space',
                'alt',
                new \Nodebox\Parc\Parser\StringParser("]"),
                'space',
            )),
            'space' => new \Nodebox\Parc\Parser\GreedyMultiParser(
                new \Nodebox\Parc\Parser\LazyAltParser(array(
                    'whitespace',
                    'comment',
                )), 0, null
            ),
            'whitespace' => new \Nodebox\Parc\Parser\RegexpParser('/^[ \\t\\r\\n]+/'),
            'comment' => new \Nodebox\Parc\Parser\RegexpParser('/^(\\(\\*\\s+[^*]*\\s+\\*\\)|\\(\\* \\*\\)|\\(\\*\\*\\))/'),
        );
        parent::__construct($s, $internals);
    }
}
