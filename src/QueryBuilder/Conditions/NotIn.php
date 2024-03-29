<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;

/**
 * Class NotIn
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotIn extends In
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::NOT_IN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid right hand of NOT IN condition';
}
