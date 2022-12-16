<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class LessThen
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class LessThen extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::LESS_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Less Then condition';
}
