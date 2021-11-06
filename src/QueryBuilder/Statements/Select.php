<?php


namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Expression;
use PDOStatement;

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

    /** @var object $resultObject */
    private mixed $resultObject;

    /**
     * Select constructor.
     * @param BuilderInterface $queryBuilder
     * @param string $tableName
     */
    public function __construct(
        private BuilderInterface $queryBuilder,
        private string $tableName,
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

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name): mixed
    {
        if (!property_exists($this, $name)) {
            throw new Exception('Invalid property name');
        }

        return $this->$name;
    }

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

    public function getArray(): array
    {
        $collection = [];
        /** @var PDOStatement $result */
        $result = $this->queryBuilder->execute((string)$this);
        $className = $this->resultObject;
        foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $collection[] = new $className(...$row);
        }
        return $collection;
    }
}