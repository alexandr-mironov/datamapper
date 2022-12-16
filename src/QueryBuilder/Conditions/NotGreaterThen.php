<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class NotGreaterThen
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotGreaterThen extends GreaterThen
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::NOT_GREATER_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Greater Then condition';
}
