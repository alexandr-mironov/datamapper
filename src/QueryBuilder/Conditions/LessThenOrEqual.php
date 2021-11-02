<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\Operators;

/**
 * Class LessThenOrEqual
 * @package DataMapper\QueryBuilder\Conditions
 */
class LessThenOrEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::LESS_THEN_OR_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments Less Then Or Equal condition';
}