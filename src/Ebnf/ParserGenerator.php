<?php

namespace Nodebox\Parc\Ebnf;

use Nodebox\Parc\Node;

class ParserGenerator
{
    /** @var EbnfParser */
    protected $parser;

    public function __construct()
    {
        $this->parser = new EbnfParser();
    }

    /**
     * Generate Parser-Code for the given ebnf
     *
     * @param string $ebnf  the ebnf
     * @param string $s     the start symbol
     * @param string $class the full qualified classname of the class o generate
     *
     * @return string the generated code
     */
    public function parser($ebnf, $s, $class)
    {
        $node = $this->parser->parse($ebnf);
        $node = $this->transform($node->result());

        $namespace = null;
        $classname = $class;
        if ( ($pos = strrpos($class, '\\')) !== false) {
            $namespace = substr($class, 0, $pos);
            $classname = substr($class, $pos + 1);
        }

        $code = "<?php\n\n";
        if ($namespace) {
            $code.= "namespace $namespace;\n\n";
        }
        $code.= "class $classname extends \Nodebox\Parc\Parser\GrammerParser\n";
        $code.= "{\n";
        $code.= "    public function __construct()\n";
        $code.= "    {\n";
        $code.= "        \$s = ".var_export((string)$s, true).";\n";
        $code.= $this->generate($node, 2);;
        $code.= "        parent::__construct(\$s, \$internals);\n";
        $code.= "    }\n";
        $code.= "}\n";

        return $code;
    }

    /**
     * Transforms and cleans the parse tree into an AST
     *
     * @param Node $node the node
     *
     * @return array The transformed node
     */
    protected function transform(Node $node)
    {
        $parserName = $node->parser()->name();
        if ( !$parserName) {
            throw new \Exception('Unable to transform node created by an unnamed parser!');
        }

        $new = array(
            'name' => $parserName,
        );
        $result = $node->result();
        switch($parserName) {
            case 'syntax':
                $new = $this->transform($result[1]);
                break;
            case 'rules':
                $new['value'] = array();
                foreach ($result as $ruleNode) {
                    $new['value'][] = $this->transform($ruleNode);
                }
                break;
            case 'rule':
                $new['value'] = array(
                    $this->transform($result[0]),
                    $this->transform($result[4]),
                );
                break;
            case 'bareword':
                $new['value'] = $result[0]->result();
                break;
            case 'alt':
            case 'conc':
                if ($result[1]->result()) {
                    $new['value'] = array(
                        $this->transform($result[0]),
                    );
                    foreach ($result[1]->result() as $termNode) {
                        $new['value'][] = $this->transform($termNode->result()[2]);
                    }
                } else {
                    $new = $this->transform($result[0]);
                }
                break;
            case 'term':
                $new = $this->transform($result);
                break;
            case 'group':
                $new = $this->transform($result[2]);
                break;
            case 'optional':
            case 'repetition':
                $new['value'] = $this->transform($result[2]);
                break;
            case 'dq':
            case 'sq':
            case 'regex':
                $new['value'] = $result[0]->result();
                break;
            default:
                throw new \Exception("Unable to transform node created by parser '{$parserName}'!");
        }

        return $new;
    }

    /**
     * Generates PHP-Code for the AST
     *
     * @param array $node   the node
     * @param int   $indent how much to indent
     *
     * @return string The code
     */
    protected function generate(array $node, $indent = 0)
    {
        $ws = ''; $is = '    '; for ($i = 0; $i < $indent; ++$i) $ws.= $is;

        $code = '';
        switch($node['name']) {
            case 'rules':
                $code.= "{$ws}\$internals = array(\n";
                foreach ($node['value'] as $rule) {
                    $code.= $this->generate($rule, $indent + 1);
                }
                $code.= "{$ws});\n";
                break;
            case 'rule':
                $code.= "{$ws}".$this->generate($node['value'][0])." => ";
                $code.= $this->generate($node['value'][1], $indent);
                $code.= ",\n";
                break;
            case 'bareword':
                $code.= var_export($node['value'], true);
                break;
            case 'alt':
                $code.= "new \Nodebox\Parc\Parser\LazyAltParser(array(\n";
                foreach ($node['value'] as $term) {
                    $code.= "{$ws}{$is}";
                    $code.= $this->generate($term, $indent + 1);
                    $code.= ",\n";
                }
                $code.= "{$ws}))";
                break;
            case 'conc':
                $code.= "new \Nodebox\Parc\Parser\ConcatParser(array(\n";
                foreach ($node['value'] as $term) {
                    $code.= "{$ws}{$is}";
                    $code.= $this->generate($term, $indent + 1);
                    $code.= ",\n";
                }
                $code.= "{$ws}))";
                break;
            case 'optional':
                $code.= "new \Nodebox\Parc\Parser\GreedyMultiParser(\n";
                $code.= "{$ws}{$is}";
                $code.= $this->generate($node['value'], $indent + 1);
                $code.= ", 0, 1\n";
                $code.= "{$ws})";
                break;
            case 'repetition':
                $code.= "new \Nodebox\Parc\Parser\GreedyMultiParser(\n";
                $code.= "{$ws}{$is}";
                $code.= $this->generate($node['value'], $indent + 1);
                $code.= ", 0, null\n";
                $code.= "{$ws})";
                break;
            case 'regex':
                $code.= "new \Nodebox\Parc\Parser\RegexpParser(";
                $code.= var_export($node['value'], true);
                $code.= ")";
                break;
            case 'dq':
                $code.= "new \Nodebox\Parc\Parser\StringParser({$node['value']})";
                break;
            case 'sq':
                $code.= "new \Nodebox\Parc\Parser\StringParser({$node['value']})";
                break;
            default:
                throw new \Exception("Unable to generate code for node '{$node['name']}'!");
        }

        return $code;
    }
}
