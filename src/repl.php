<?php

require_once("lexer.php");

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
        print_r($tokens).PHP_EOL;
    }
}

main();
