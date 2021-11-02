<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\Operators;

/**
 * Class NotLike
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid condition parameters';
}