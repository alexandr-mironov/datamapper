<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Exceptions\Exception;
use Micro\Core\QueryBuilder\Operators;

/**
 * Class In
 * @package unshort\core\QueryBuilder\Conditions
 */
class In extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::IN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid right hand of IN condition';

    /**
     * @inheritDoc
     */
    public function __construct(array $conditionParts)
    {
        [$left, $right] = $conditionParts;
        if (!is_array($right)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }
        foreach ($right as &$el) {
            $el = $this->quote($el);
        }
        $this->condition = $this->quote($left)
            . static::CONDITION_OPERATOR
            . '('
            . implode(',', $right)
            . ')';
    }
}