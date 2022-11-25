<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

/**
 * Interface StatementInterface
 *
 * @package DataMapper\QueryBuilder\Statements
 */
interface StatementInterface
{
    /**
     * @return string
     */
    public function __toString(): string;
}
