<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class NotLike
 * @package unshort\core\QueryBuilder\Conditions
 */
class NotLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid condition parameters';
}