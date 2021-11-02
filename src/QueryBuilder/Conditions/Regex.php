<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\MySQL\Operators;

class Regex extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::RLIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for ' . self::CONDITION_OPERATOR . ' condition';
}