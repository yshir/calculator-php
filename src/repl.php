<?php

require_once("evaluator.php");
require_once("lexer.php");
require_once("parser.php");

function main()
{
    while (1) {
        echo '> ';

        $stdin = trim(fgets(STDIN));
        if ($stdin === '') {
            exit;
        }

        $lexer = new Lexer();
        $tokens = $lexer->lex($stdin);
        // print_r($tokens);

        $parser = new Parser();
        $ast = $parser->parse($tokens);
        // print_r($ast);

        $evaluator = new Evaluator();
        $result = $evaluator->eval($ast);
        echo $result.PHP_EOL;
    }
}

main();
