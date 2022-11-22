<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

use DataMapper\QueryBuilder\Statements\StatementInterface;

/**
 * Interface BuilderInterface
 *
 * @package DataMapper\QueryBuilder
 */
interface BuilderInterface extends BuilderWrapperInterface
{
    public function getInsert(): StatementInterface;
}
