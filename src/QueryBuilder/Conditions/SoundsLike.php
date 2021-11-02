<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\MySQL\Operators;

/**
 * Class SoundsLike
 * @package unshort\core\QueryBuilder\Conditions
 */
class SoundsLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::SOUNDS_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for ' . self::CONDITION_OPERATOR . ' condition';
}