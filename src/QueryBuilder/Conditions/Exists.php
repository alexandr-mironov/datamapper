<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;

/**
 * Class Exists
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class Exists extends IsNull
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::EXISTS;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Exists condition';
}
