<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;

/**
 * Class NotLessThen
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotLessThen extends LessThen
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::NOT_LESS_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Less Then condition';
}
