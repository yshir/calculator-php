<?php

require_once('lexer.php');
require_once('parser.php');

class Evaluator
{
    public function eval(Node $node): int
    {
        switch (get_class($node)) {
            case NumNode::class:
                return $node->value;
            case UnaryNode::class:
                return $this->evalUnaryNode(
                    $node->op,
                    $this->eval($node->rhs)
                );
            case BinaryNode::class:
                return $this->evalBinaryNode(
                    $node->op,
                    $this->eval($node->lhs),
                    $this->eval($node->rhs)
                );
            default:
                throw new Exception('unreachable');
        }
    }

    private function evalBinaryNode(BinaryOp $op, int $lhs, int $rhs): int
    {
        switch ($op->kind) {
            case BinaryOp::ADD:
                return $lhs + $rhs;
            case BinaryOp::SUB:
                return $lhs - $rhs;
            case BinaryOp::MUL:
                return $lhs * $rhs;
            case BinaryOp::DIV:
                if ($rhs === 0) {
                    throw new Exception('division by zero');
                }
                return $lhs / $rhs;
            default:
                throw new Exception('unreachable');
        }
    }

    private function evalUnaryNode(UnaryOp $op, int $value): int
    {
        switch ($op->kind) {
            case UnaryOp::PLUS:
                return $value;
            case UnaryOp::MINUS:
                return $value * -1;
            default:
                throw new Exception('unreachable');
        }
    }
}
