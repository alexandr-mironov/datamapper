<?php


namespace Micro\Core\QueryBuilder\Conditions;

use Micro\Core\QueryBuilder\Exceptions\Exception;
use Micro\Core\QueryBuilder\Operators;

/**
 * Class Equal
 * @package unshort\core\QueryBuilder\Conditions
 */
class Equal extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Equal condition required two elements';

    /**
     * Equal constructor.
     * @param array $conditionParts
     * @throws Exception
     */
    public function __construct(array $conditionParts)
    {
        [$left, $right] = $conditionParts;
        if (!$left || !isset($left, $right)) {
            throw new Exception(static::EXCEPTION_MESSAGE ?? 'Invalid argument');
        }
        parent::__construct([$left, $right]);
    }
}