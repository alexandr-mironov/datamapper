<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use DataMapper\Exceptions\Exception;
use DataMapper\QueryBuilder\Conditions\NotEqual;
use DataMapper\QueryBuilder\Operators;
use DataMapper\QueryBuilder\QueryBuilder;
use DataMapper\QueryBuilder\Statements\Select;
use PDO;
use PHPUnit\Framework\TestCase;
use Test\TestEntity;

class QueryBuilderTest extends TestCase
{
    /**
     * @var DataMapper
     */
    private DataMapper $dataMapper;

    public function setUp(): void
    {
        $pdo = $this->createMock(PDO::class);
        $this->dataMapper = DataMapper::construct($pdo, QueryBuilder::SQL1999, false);
    }

    /**
     * @covers \DataMapper\QueryBuilder\QueryBuilder::select
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::by
     * @covers \DataMapper\QueryBuilder\Statements\Select::__toString
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::addWhereCondition
     * @throws Exception
     * @throws \DataMapper\QueryBuilder\Exceptions\Exception
     */
    public function testSelect(): void
    {
        $queryBuilder = new QueryBuilder();
        $reflection = new \ReflectionClass(TestEntity::class);

        $select = $queryBuilder->select($this->dataMapper->getTable($reflection));
        $this->assertTrue($select instanceof Select);
        $this->assertEquals('SELECT * FROM `some_database`.`user`', (string)$select);
        $select->by('some_field', 'some_value');
        $this->assertEquals("SELECT * FROM `some_database`.`user` WHERE 'some_field' = 'some_value'", (string)$select);
        $select->addWhereCondition(new NotEqual(['another_column', 'another_value']), Operators::OR);
        $this->assertEquals(
            "SELECT * FROM `some_database`.`user` WHERE 'some_field' = 'some_value' OR 'another_column' != 'another_value'",
            (string)$select
        );
    }
}
