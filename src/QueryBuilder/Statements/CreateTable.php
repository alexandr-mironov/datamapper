<?php


namespace Micro\Core\QueryBuilder\Statements;


use Micro\Core\QueryBuilder\Definitions\Column;

/**
 * Class CreateTable
 * @package unshort\core\QueryBuilder\Statements
 */
class CreateTable implements StatementInterface
{
    /** @var bool|mixed */
    public bool $temporary = false;

    /** @var bool|mixed */
    public bool $ifNotExists = false;

    /** @var array */
    private array $columns = [];

    /** @var array */
    public array $options = [];

    /** @var array */
    public array $partitionOptions = [];

    /**
     * CreateTable constructor.
     * @param string $tableName
     * @param array $options
     */
    public function __construct(
        private string $tableName,
        array $options = [],
    )
    {
        $this->temporary = $options['temporary'] ?? false;
        $this->ifNotExists = $options['ifNotExists'] ?? false;
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
        $query .= $this->tableName . " (\n" . implode(",\n", $this->columns) . "\n)";

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
     */
    public function addColumn(Column $columnDefinition)
    {
        $this->columns[] = $columnDefinition->__toString();
    }

    /**
     * @param Column ...$columns
     */
    public function addColumns(Column ...$columns)
    {
        foreach ($columns as $column) {
            $this->columns[] = $column->__toString();
        }
    }
}