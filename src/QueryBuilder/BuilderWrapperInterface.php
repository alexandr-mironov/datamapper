<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

use DataMapper\Entity\FieldCollection;
use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Statements\Select;

interface BuilderWrapperInterface
{
    /**
     * @param string $table
     * @param array<mixed> $values
     *
     * @return int
     */
    public function insert(string $table, array $values): int;

    /**
     * @param Table $table
     * @param string $className
     *
     * @return Select
     */
    public function find(Table $table, string $className): Select;

    /**
     * @param Table $table
     * @param FieldCollection $values
     * @param string[] $updatable
     *
     * @return bool
     * @throws Exception
     */
    public function insertUpdate(Table $table, FieldCollection $values, array $updatable): bool;
}
