<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;

/**
 * Class NotGreaterThen
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotGreaterThen extends GreaterThen
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::NOT_GREATER_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Greater Then condition';
}
