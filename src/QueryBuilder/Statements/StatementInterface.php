<?php


namespace DataMapper\QueryBuilder\Statements;

/**
 * Interface StatementInterface
 * @package DataMapper\QueryBuilder\Statements
 */
interface StatementInterface
{
    /**
     * @return string
     */
    public function __toString(): string;
}