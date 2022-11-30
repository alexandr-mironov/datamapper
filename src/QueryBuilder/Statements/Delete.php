<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;

/**
 * Class Delete
 *
 * @package DataMapper\QueryBuilder\Statements
 */
class Delete extends AbstractStatementWithWhere implements StatementInterface
{
    /**
     * Delete constructor.
     *
     * @param Table $table
     * @param ConditionInterface ...$conditions
     */
    public function __construct(
        private Table $table,
        ConditionInterface ...$conditions
    ) {
        foreach ($conditions as $condition) {
            $this->addWhereCondition($condition);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'DELETE FROM ' . $this->table->getName() . ' WHERE ' . $this->buildWhereStatement();
    }
}
