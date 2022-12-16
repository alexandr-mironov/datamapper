<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class GreaterThenOrEqual
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class GreaterThenOrEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::GREATER_THEN_OR_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Greater Then Or Equal condition';
}
