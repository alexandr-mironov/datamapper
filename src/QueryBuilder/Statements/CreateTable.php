<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Definitions\Column;
use Exception;

/**
 * Class CreateTable
 *
 * @package DataMapper\QueryBuilder\Statements
 */
class CreateTable implements StatementInterface
{
    /** @var bool */
    public bool $temporary = false;

    /** @var bool */
    public bool $ifNotExists = false;

    /** @var array<mixed> */
    public array $options = [];

    /** @var string[] */
    public array $partitionOptions = [];

    /** @var string[] */
    private array $columns = [];

    /**
     * CreateTable constructor.
     *
     * @param Table $tableName
     * @param array<mixed> $options
     */
    public function __construct(
        private Table $tableName,
        array $options = [],
    ) {
        $this->temporary = (bool)($options['temporary'] ?? false);
        $this->ifNotExists = (bool)($options['ifNotExists'] ?? false);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $query = 'CREATE ';
        if ($this->temporary) {
            $query .= 'TEMPORARY ';
        }
        $query .= 'TABLE ';
        if ($this->ifNotExists) {
            $query .= 'IF NOT EXISTS ';
        }
        $query .= $this->tableName->getName() . " (\n" . implode(",\n", $this->columns) . "\n)";

        if ($this->options) {
            $query .= "\n" . implode(" ", $this->options);
        }

        if ($this->partitionOptions) {
            $query .= "\n" . implode(" ", $this->partitionOptions);
        }

        return $query;
    }

    /**
     * @param Column $columnDefinition
     *
     * @throws Exception
     */
    public function addColumn(Column $columnDefinition): void
    {
        $this->columns[] = $columnDefinition->__toString();
    }

    /**
     * @param Column ...$columns
     *
     * @throws Exception
     */
    public function addColumns(Column ...$columns): void
    {
        foreach ($columns as $column) {
            $this->columns[] = $column->__toString();
        }
    }
}
