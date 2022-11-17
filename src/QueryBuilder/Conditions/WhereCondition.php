<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

/**
 * Class WhereCondition
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class WhereCondition implements ConditionInterface
{
    /** @var array */
    private array $conditions = [];

    /**
     * WhereCondition constructor.
     *
     * @param array<mixed> $conditions
     */
    public function __construct(array $conditions)
    {
        if (isset($conditions[0]) && is_array($conditions[0])) {
            foreach ($conditions as $condition) {
                $this->parseCondition($condition);
            }
        } else {
            $this->parseCondition($conditions);
        }
    }

    /**
     * @param array $condition
     */
    private function parseCondition(array $condition)
    {

    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // TODO: Implement getCondition() method.
    }
}
