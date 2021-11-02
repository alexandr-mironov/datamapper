<?php


namespace Micro\Core\QueryBuilder;


/**
 * Class Expression
 * @package unshort\core\QueryBuilder
 */
class Expression
{
    /**
     * Expression constructor.
     * @param string $expression
     */
    public function __construct(
        private string $expression
    )
    {

    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->expression;
    }
}