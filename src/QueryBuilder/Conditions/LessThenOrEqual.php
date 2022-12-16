<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class LessThenOrEqual
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class LessThenOrEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::LESS_THEN_OR_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments Less Then Or Equal condition';
}
