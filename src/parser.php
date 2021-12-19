<?php

require_once('lexer.php');

interface Node
{
}

class NumNode implements Node
{
    public int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}

class UnaryNode implements Node
{
    public UnaryOp $op;
    public Node $rhs;

    public function __construct(UnaryOp $op, Node $rhs)
    {
        $this->op = $op;
        $this->rhs = $rhs;
    }
}

class BinaryNode implements Node
{
    public BinaryOp $op;
    public Node $lhs;
    public Node $rhs;

    public function __construct(BinaryOp $op, Node $lhs, Node $rhs)
    {
        $this->op = $op;
        $this->lhs = $lhs;
        $this->rhs = $rhs;
    }
}

class UnaryOp
{
    public const PLUS = "+";
    public const MINUS = "-";

    public string $kind;

    public function __construct(string $kind)
    {
        $this->kind = $kind;
    }
}

class BinaryOp
{
    public const ADD = "+";
    public const SUB = "-";
    public const MUL = "*";
    public const DIV = "/";

    public string $kind;

    public function __construct(string $kind)
    {
        $this->kind = $kind;
    }
}

class Parser
{
    private array $tokens;
    private int $pos;

    // program = expr ;
    // expr = term ("+"|"-" term)* ;
    // term = unary ("*"|"/" unary)* ;
    // unary = ("+"|"-")? factor ;
    // factor = number | "(" expr ")" ;
    public function parse(array $tokens): Node
    {
        $this->tokens = $tokens;
        $this->pos = 0;

        return $this->parseProgram();
    }

    // program = expr ;
    private function parseProgram(): Node
    {
        return $this->parseExpr();
    }

    // expr = term ("+"|"-" term)* ;
    private function parseExpr(): Node
    {
        $lhs = $this->parseTerm();
        while (1) {
            if ($this->peek(Token::PLUS)) {
                $this->pos++;
                $lhs = new BinaryNode(new BinaryOp(BinaryOp::ADD), $lhs, $this->parseTerm());
            } elseif ($this->peek(Token::MINUS)) {
                $this->pos++;
                $lhs = new BinaryNode(new BinaryOp(BinaryOp::SUB), $lhs, $this->parseTerm());
            } else {
                return $lhs;
            }
        }
    }

    // term = unary ("*"|"/" unary)* ;
    private function parseTerm(): Node
    {
        $lhs = $this->parseUnary();
        while (1) {
            if ($this->peek(Token::ASTERISK)) {
                $this->pos++;
                $lhs = new BinaryNode(new BinaryOp(BinaryOp::MUL), $lhs, $this->parseUnary());
            } elseif ($this->peek(Token::SLASH)) {
                $this->pos++;
                $lhs = new BinaryNode(new BinaryOp(BinaryOp::DIV), $lhs, $this->parseUnary());
            } else {
                return $lhs;
            }
        }
    }

    // unary = ("+"|"-")? factor ;
    private function parseUnary(): Node
    {
        if ($this->peek(Token::PLUS)) {
            $this->pos++;
            return new UnaryNode(new UnaryOp(UnaryOp::PLUS), $this->parseFactor());
        } elseif ($this->peek(Token::MINUS)) {
            $this->pos++;
            return new UnaryNode(new UnaryOp(UnaryOp::MINUS), $this->parseFactor());
        } else {
            return $this->parseFactor();
        }
    }

    // factor = number | "(" expr ")" ;
    private function parseFactor(): Node
    {
        if ($this->peek(Token::NUM)) {
            $node = new NumNode($this->token()->value);
            $this->pos++;
            return $node;
        }

        $this->expect(Token::LPAREN);
        $expr = $this->parseExpr();
        $this->expect(Token::RPAREN);
        return $expr;
    }

    // 次に読むトークン
    private function token() : ?Token
    {
        return $this->tokens[$this->pos];
    }

    // 次に読むトークンを確認する。期待と一致していれば true
    private function peek(string $tokenKind): bool
    {
        return $this->token() && $this->token()->kind === $tokenKind;
    }

    // トークンを1つ読みすすめる。ただし期待と一致してなければエラー
    private function expect(string $tokenKind): void
    {
        if (!$this->peek($tokenKind)) {
            throw new Exception($tokenKind." expected, but got ".$this->token()->kind);
        }
        $this->pos++;
    }
}
