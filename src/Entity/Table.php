<?php

declare(strict_types=1);

namespace DataMapper\Entity;

class Table
{
    /**
     * @param string $tableName
     */
    public function __construct(
        private string $tableName
    ) {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->tableName;
    }
}
