<?php


namespace DataMapper\QueryBuilder\Statements;

use DataMapper\QueryBuilder\Exceptions\Exception;

/**
 * Class SelectWrapper
 * @package DataMapper\QueryBuilder\Statements
 *
 * @property-read Select $statement
 * @property-read string $alias
 */
class SelectWrapper
{
    /**
     * SelectWrapper constructor.
     * @param Select $statement
     * @param string|null $alias
     */
    public function __construct(
        private Select $statement,
        private ?string $alias = null,
    )
    {
        if ($this->alias === null) {
            $this->alias = $this->createAlias();
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name): mixed
    {
        if (!isset($this->$name)) {
            throw new Exception('Invalid property name');
        }

        return $this->$name;
    }

    /**
     * @return string
     */
    private function createAlias(): string
    {
        return uniqid($this->statement->tableName . '_');
    }
}