<?php

declare(strict_types=1);

namespace DataMapper\Entity;

class Table
{
    public function __construct(private string $tableName)
    {

    }

    public function getName(): string
    {
        return $this->tableName;
    }
}