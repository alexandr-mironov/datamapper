<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\Entity\Table;

/**
 * Class AlterTable
 *
 * @package DataMapper\QueryBuilder\Statements
 */
class AlterTable implements StatementInterface
{
    /** @var AlterOption[] */
    private array $options = [];

    /**
     * AlterTable constructor.
     *
     * @param Table $table
     * @param AlterOption ...$options
     */
    public function __construct(
        private Table $table,
        AlterOption ...$options
    ) {
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
        return "ALTER TABLE {$this->table->getName()} " . implode(', ', $this->options);
    }
}
