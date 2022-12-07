<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

/**
 * Class AbstractStatementWithWhere
 *
 * @package DataMapper\QueryBuilder\Statements
 */
abstract class AbstractStatementWithWhere
{
    use WhereTrait;
}
