<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class LessThenOrEqual
 * @package unshort\core\QueryBuilder\Conditions
 */
class LessThenOrEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::LESS_THEN_OR_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments Less Then Or Equal condition';
}