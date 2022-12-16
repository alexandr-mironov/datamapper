<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class NotEqual
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::NOT_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments Not Equal condition';
}
