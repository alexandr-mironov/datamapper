<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;


use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Expression;
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
    private array $order = [];

    /**
     * Select constructor.
     *
     * @param BuilderInterface $queryBuilder
     * @param Table $table
     * @param string $resultObject
     */
    public function __construct(
        private BuilderInterface $queryBuilder,
        public Table $table,
        private string $resultObject,
    ) {
        $this->selectExpression = new Expression('*');
    }

    /**
     * @param string[] $fields
     */
    public function fieldSet(array $fields)
    {
        $this->selectExpression = implode(', ', $fields);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $queryParts = ['SELECT', $this->selectExpression, 'FROM ' . $this->table->getName(), 'WHERE'];
        if (count($this->wheres)) {
            $queryParts[] = $this->buildWhereStatement();
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

    /**
     * @return object
     * @throws Exception
     */
    public function getOne(): object
    {
        $this->limit = 1;
        $result = $this->queryBuilder->execute((string)$this);
        $className = $this->resultObject;

        return new $className(...$result);
    }

    public function order(string|array $order): static
    {
        switch (true) {
            case is_string($order):
                $this->order[] = [
                    $order => 'DESC',
                ];
                break;
            default:
                $this->order = $order;
        }

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getArray(): array
    {
        $collection = [];
        foreach ($this->getIterator() as $item) {
            $collection[] = $item;
        }

        return $collection;
    }

    /**
     * @return Generator
     * @throws Exception
     */
    public function getIterator(): Generator
    {
        $result = $this->queryBuilder->execute((string)$this);
        $className = $this->resultObject;
        foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
            yield new $className(...$row);
        }
    }

    /**
     * @param string $key
     *
     * @return array
     * @throws Exception
     */
    public function getMap(string $key): array
    {
        $collection = [];
        foreach ($this->getIterator() as $item) {
            if (!property_exists($item, $key)) {
                throw new Exception('`' . $key . '` is not in field list');
            }
            $collection[$item->$key] = $item;
        }

        return $collection;
    }
}
