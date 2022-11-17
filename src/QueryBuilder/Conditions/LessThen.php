<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Operators;

/**
 * Class LessThen
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class LessThen extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::LESS_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Less Then condition';
}
