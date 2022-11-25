<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

/**
 * Class With
 *
 * @package DataMapper\QueryBuilder\Statements
 */
class With implements StatementInterface
{
    /** @var bool */
    public bool $recursive = false;

    /** @var SelectWrapper[] */
    private array $selects = [];

    /** @var string[] */
    private array $aliases = [];

    /**
     * With constructor.
     *
     * @param SelectWrapper ...$selects
     */
    public function __construct(SelectWrapper ...$selects)
    {
        $this->selects = $selects;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $query = "WITH" . ($this->recursive ? ' RECURSIVE ' : '') . "\n";
        $queries = [];
        foreach ($this->selects as $select) {
            $this->aliases[] = $select->alias;
            $queries[] = $select->alias . ' AS (' . $select->statement . ')';
        }

        return $query . implode(",\n", $queries);
    }
}
