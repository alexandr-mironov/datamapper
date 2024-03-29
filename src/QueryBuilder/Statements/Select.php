<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Expression;
use DataMapper\QueryBuilder\QueryBuilder;
use Generator;
use PDO;

/**
 * Class Select
 *
 * @package DataMapper\QueryBuilder\Statements
 */
class Select extends AbstractStatementWithWhere implements StatementInterface
{
    /** @var string|Expression */
    public string|Expression $selectExpression;

    /** @var array<mixed> $order */
    public array $order = [];

    /**
     * Select constructor.
     *
     * @param Table $table
     * @param ConditionInterface ...$conditions
     */
    public function __construct(
        public Table $table,
        ConditionInterface ...$conditions
    ) {
        $this->selectExpression = new Expression('*');

        foreach ($conditions as $condition) {
            $this->addWhereCondition($condition);
        }
    }

    /**
     * @param string[] $fields
     *
     * @return Select
     */
    public function fieldSet(array $fields): Select
    {
        $this->selectExpression = implode(', ', $fields);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // todo: don't add WHERE keyword if has no where statements
        $queryParts = ['SELECT', $this->selectExpression, 'FROM ' . $this->table->getName()];
        if (count($this->wheres)) {
            $queryParts[] = 'WHERE ' . $this->buildWhereStatement();
        }
        if ($this->order) {
            $queryParts[] = ' ORDER BY ' . implode(', ', $this->order);
        }
        if ($this->limit) {
            $queryParts[] = 'LIMIT ' . $this->limit;
            if ($this->offset) {
                $queryParts[] = 'OFFSET ' . $this->offset;
            }
        }

        return implode(($this->queryBuilder->beautify ?? false) ? PHP_EOL : ' ', $queryParts);
    }
}
