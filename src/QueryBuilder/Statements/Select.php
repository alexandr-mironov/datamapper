<?php


namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Expression;

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

    /** @var int|null $limit */
    public ?int $limit = null;

    /** @var int|null $offset */
    public ?int $offset = null;


    /**
     * Select constructor.
     * @param string $tableName
     */
    public function __construct(
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

    public function getOne()
    {
        $this->limit = 1;
        $result = $this->execute();
        $className = $this->returnObject;
        return new $className(...$result);
    }

    public function getArray()
    {

    }
}