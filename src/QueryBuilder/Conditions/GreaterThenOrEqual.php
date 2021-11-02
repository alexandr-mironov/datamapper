<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class GreaterThenOrEqual
 * @package unshort\core\QueryBuilder\Conditions
 */
class GreaterThenOrEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::GREATER_THEN_OR_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Greater Then Or Equal condition';
}