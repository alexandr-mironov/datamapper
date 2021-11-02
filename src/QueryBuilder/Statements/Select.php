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
}