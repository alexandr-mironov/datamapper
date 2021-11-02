<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\MySQL\Operators;

/**
 * Class SoundsLike
 * @package DataMapper\QueryBuilder\Conditions
 */
class SoundsLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::SOUNDS_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for ' . self::CONDITION_OPERATOR . ' condition';
}