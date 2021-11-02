<?php


namespace DataMapper\QueryBuilder\Statements;

/**
 * Class AlterTable
 * @package DataMapper\QueryBuilder\Statements
 */
class AlterTable implements StatementInterface
{
    /** @var AlterOption[] */
    private array $options = [];

    /**
     * AlterTable constructor.
     * @param string $tableName
     * @param AlterOption ...$options
     */
    public function __construct(
        private string $tableName,
        AlterOption ...$options
    )
    {
        $this->options = $options;
    }

    /**
     * @param AlterOption $option
     */
    public function addOption(AlterOption $option): void
    {
        $this->options[] = $option;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return "ALTER TABLE {$this->tableName} " . implode(', ', $this->options);
    }
}