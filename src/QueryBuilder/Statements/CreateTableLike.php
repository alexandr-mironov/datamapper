<?php


namespace DataMapper\QueryBuilder\Statements;


/**
 * Class CreateTableLike
 * @package DataMapper\QueryBuilder\Statements
 */
class CreateTableLike implements StatementInterface
{
    /**
     * CreateTableLike constructor.
     * @param string $newTableName
     * @param string $originalTableName
     */
    public function __construct(
        private string $newTableName,
        private string $originalTableName
    )
    {

    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'CREATE TABLE {$this->newTableName} LIKE {$this->originalTableName};';
    }
}