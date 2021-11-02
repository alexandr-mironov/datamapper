<?php


namespace Micro\Core\QueryBuilder\Statements;


class DropTable implements StatementInterface
{
    /** @var bool  */
    public bool $ifExists = false;

    /** @var bool  */
    public bool $temporary = false;

    /** @var bool  */
    public bool $restrict = false;

    /**
     * @var bool
     */
    public bool $cascade = false;

    /** @var string[]  */
    private array $tableNames;

    /**
     * DropTable constructor.
     *
     * @param array ...$tables
     */
    public function __construct(array ...$tables)
    {
        $this->tableNames = $tables;
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
            .  "TABLE "
            . (($this->ifExists) ? 'IF EXISTS ' : '')
            . implode(', ', $this->tableNames)
            . (($this->restrict && $this->cascade === false) ? ' RESTRICT' : '')
            . (($this->cascade) ? ' CASCADE': '')
            . ';';
    }
}