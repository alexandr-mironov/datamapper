<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\Entity\Table;

class DropTable implements StatementInterface
{
    /** @var bool */
    public bool $ifExists = false;

    /** @var bool */
    public bool $temporary = false;

    /** @var bool */
    public bool $restrict = false;

    /**
     * @var bool
     */
    public bool $cascade = false;

    /** @var string[] */
    private array $tableNames;

    /**
     * DropTable constructor.
     *
     * @param Table ...$tables
     */
    public function __construct(Table ...$tables)
    {
        foreach ($tables as $table) {
            $this->tableNames[] = $table->getName();
        }
    }

    /**
     * @inheritDoc
     *
     * DROP [TEMPORARY] TABLE [IF EXISTS] tbl_name [, tbl_name] ... [RESTRICT | CASCADE]
     */
    public function __toString(): string
    {
        return "DROP "
            . (($this->temporary) ? 'TEMPORARY ' : '')
            . "TABLE "
            . (($this->ifExists) ? 'IF EXISTS ' : '')
            . implode(', ', $this->tableNames)
            . (($this->restrict && $this->cascade === false) ? ' RESTRICT' : '')
            . (($this->cascade) ? ' CASCADE' : '')
            . ';';
    }
}
