<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\Operators;

/**
 * Class NotEqual
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments Not Equal condition';
}