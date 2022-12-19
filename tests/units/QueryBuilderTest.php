<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use DataMapper\Exceptions\Exception;
use DataMapper\QueryBuilder\Conditions\NotEqual;
use DataMapper\QueryBuilder\Exceptions\UnsupportedException;
use DataMapper\QueryBuilder\LogicalOperators;
use DataMapper\QueryBuilder\QueryBuilder;
use DataMapper\QueryBuilder\Statements\Insert;
use DataMapper\QueryBuilder\Statements\Select;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Test\TestEntity;

class QueryBuilderTest extends TestCase
{
    /**
     * @var DataMapper
     */
    private DataMapper $dataMapper;

    /**
     * @throws \DataMapper\QueryBuilder\Exceptions\Exception
     * @throws UnsupportedException
     */
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
        $reflection = new ReflectionClass(TestEntity::class);

        $select = $queryBuilder->select($this->dataMapper->getTable($reflection));
        $this->assertTrue($select instanceof Select);
        $this->assertEquals('SELECT * FROM `some_database`.`user`', (string)$select);
        $select->by('some_field', 'some_value');
        $this->assertEquals("SELECT * FROM `some_database`.`user` WHERE 'some_field' = 'some_value'", (string)$select);
        $select->addWhereCondition(new NotEqual(['another_column', 'another_value']), LogicalOperators:: OR);
        $this->assertEquals(
            "SELECT * FROM `some_database`.`user` WHERE 'some_field' = 'some_value' OR 'another_column' != 'another_value'",
            (string)$select
        );
    }

    /**
     * @dataProvider selectProvider
     *
     * @covers \DataMapper\QueryBuilder\QueryBuilder::select
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::by
     * @covers \DataMapper\QueryBuilder\Statements\Select::__toString
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::addWhereCondition
     *
     * @param Select $select
     * @param string $expected
     */
    public function testSelect2(Select $select, string $expected): void
    {
        $this->assertEquals($expected, (string)$select);
    }

    public function selectProvider(): array
    {
        $this->setUp();
        $queryBuilder = new QueryBuilder();
        $reflection = new ReflectionClass(TestEntity::class);

        $select = $queryBuilder->select($this->dataMapper->getTable($reflection));

        return [
            [
                'select' => $select,
                'expected' => 'SELECT * FROM `some_database`.`user`'
            ],
            [
                'select' => (clone $select)->by('some_field', 'some_value'),
                'expected' => "SELECT * FROM `some_database`.`user` WHERE 'some_field' = 'some_value'"
            ],
            [
                'select' => (clone $select)
                    ->by('some_field', 'some_value')
                    ->addWhereCondition(new NotEqual(['another_column', 'another_value']), LogicalOperators:: OR),
                'expected' => "SELECT * FROM `some_database`.`user` WHERE 'some_field' = 'some_value' OR 'another_column' != 'another_value'"
            ]
        ];
    }

    /**
     * @covers \DataMapper\QueryBuilder\Statements\Insert
     * @covers \DataMapper\QueryBuilder\Statements\Insert::__toString
     * @covers \DataMapper\QueryBuilder\Statements\Insert::addValues
     * @throws Exception
     */
    public function testInsert(): void
    {
        $reflection = new ReflectionClass(TestEntity::class);
        $table = $this->dataMapper->getTable($reflection);

        $insert = new Insert(
            $table->getName(),
            ['id'],
        );
        $this->assertEquals("INSERT INTO `some_database`.`user` (id) VALUES (:id);", (string)$insert);

        $insert->addValues('username');
        $this->assertEquals(
            "INSERT INTO `some_database`.`user` (id, username) VALUES (:id, :username);",
            (string)$insert
        );

        $insert->addValues('firstname', 'lastname');
        $this->assertEquals(
            "INSERT INTO `some_database`.`user` (id, username, firstname, lastname) VALUES (:id, :username, :firstname, :lastname);",
            (string)$insert
        );
    }
}
