<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Expression;
use Generator;

/**
 * Class Select
 * @package DataMapper\QueryBuilder\Statements
 *
 * @property-read string $tableName
 */
class Select extends AbstractStatementWithWhere implements StatementInterface
{
    /** @var string|Expression */
    public string|Expression $selectExpression;

    /** @var array $order */
    private array $order = [];

    /**
     * Select constructor.
     * @param BuilderInterface $queryBuilder
     * @param string $tableName
     * @param string $resultObject
     */
    public function __construct(
        private BuilderInterface $queryBuilder,
        private string $tableName,
        private string $resultObject,
    )
    {
        $this->selectExpression = new Expression('*');
    }

    /**
     * @param array $fields
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
        $query = 'SELECT ';
        $query .= $this->selectExpression;
        $query .= ' FROM ' . $this->tableName;
        if (count($this->wheres)) {
            $query .= $this->buildWhereStatement();
        }
        if ($this->order) {
            $query .= ' ORDER BY ' . implode(', ', $this->order);
        }
        if ($this->limit) {
            $query .= ' LIMIT ' . $this->limit;
            if ($this->offset) {
                $query .= ' OFFSET ' . $this->offset;
            }
        }
        return $query;
    }

//    /**
//     * @param $name
//     * @return mixed
//     * @throws Exception
//     */
//    public function __get($name): mixed
//    {
//        if (!property_exists($this, $name)) {
//            throw new Exception('Invalid property name');
//        }
//
//        return $this->$name;
//    }

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
        foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            yield new $className(...$row);
        }
    }
}