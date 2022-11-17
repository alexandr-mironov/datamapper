<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Operators;

/**
 * Class GreaterThen
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class GreaterThen extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::GREATER_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for greater then condition';
}
