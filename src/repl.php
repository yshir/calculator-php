<?php

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
        print_r($ast);
    }
}

main();
