<?php


namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Operators;

/**
 * Class NotGreaterThen
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotGreaterThen extends GreaterThen
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_GREATER_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Greater Then condition';
}