<?php


namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Operators;

/**
 * Class NotExists
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotExists extends Exists
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_EXISTS;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Exists condition';
}