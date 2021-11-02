<?php


namespace Micro\Core\QueryBuilder\Statements;

/**
 * Class With
 * @package unshort\core\QueryBuilder\Statements
 */
class With implements StatementInterface
{
    /** @var SelectWrapper[] */
    private array $selects = [];

    /** @var string[] */
    private array $aliases = [];

    /** @var bool */
    public bool $recursive = false;

    /**
     * With constructor.
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