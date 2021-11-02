<?php


namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Operators;

/**
 * Class NotLessThen
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotLessThen extends LessThen
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_LESS_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Less Then condition';
}