<?php

class Token
{
    const PLUS = "+";
    const MINUS = "-";
    const ASTERISK = "*";
    const SLASH = "/";
    const LPAREN = "(";
    const RPAREN = ")";
    const NUM = "NUM";

    public string $kind;
    public ?int $value; // num のときは数値が入る

    public function __construct(string $kind, ?int $value = null)
    {
        $this->kind = $kind;
        $this->value = $value;
    }
}

class Lexer
{
    private string $input;
    private int $pos;

    /**
     * @return Token[]
     */
    public function lex(string $input): array
    {
        $this->input = $input;
        $this->pos = 0;

        $tokens = [];

        while ($this->char()) {
            $this->skipSpaces();

            $ch = $this->char();
            switch ($ch) {
                case '+':
                    $tokens[] = new Token(Token::PLUS); $this->pos++; break;
                case '-':
                    $tokens[] = new Token(Token::MINUS); $this->pos++; break;
                case '*':
                    $tokens[] = new Token(Token::ASTERISK); $this->pos++; break;
                case '/':
                    $tokens[] = new Token(Token::SLASH); $this->pos++; break;
                case '(':
                    $tokens[] = new Token(Token::LPAREN); $this->pos++; break;
                case ')':
                    $tokens[] = new Token(Token::RPAREN); $this->pos++; break;
                default:
                    if (preg_match('/^[0-9]$/', $ch)) {
                        $tokens[] = new Token(Token::NUM, $this->readNumber());
                        break;
                    }
                    throw new Exception("invalid char: {$ch}");
            }
        }

        return $tokens;
    }

    // 現在の文字を返す (最後まで読み終えていれば NULL を返す)
    private function char(): ?string
    {
        return $this->input[$this->pos];
    }

    // 空白文字を読み飛ばす
    private function skipSpaces(): void
    {
        while ($this->char() && ctype_space($this->char())) {
            $this->pos++;
        }
    }

    // 数値を読んで返す
    private function readNumber(): int
    {
        $prev = $this->pos;
        while ($this->char() && preg_match('/^[0-9]$/', $this->char())) {
            $this->pos++;
        }
        return (int) mb_substr($this->input, $prev, $this->pos - $prev);
    }
}
