<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use DataMapper\Exceptions\Exception;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\Exceptions\Exception as QueryBuilderException;
use DataMapper\QueryBuilder\Operators;
use DataMapper\QueryBuilder\QueryBuilder;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Test\TestEntity;

class SelectTest extends TestCase
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
     * @covers \DataMapper\DataMapper::getTable
     * @throws Exception
     */
    public function testAttributeTable(): void
    {
        $table = $this->dataMapper->getTable(new ReflectionClass(TestEntity::class));
        $this->assertEquals('`some_database`.`user`', $table->getName());
    }

    /**
     * @covers \DataMapper\DataMapper::find
     * @throws Exception
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function testSimpleQueryBuilding(): void
    {
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value')
            ->getSQL();
        $this->assertEquals("SELECT * FROM `some_database`.`user` WHERE 'field'='value'", $result);
        $result = $this->dataMapper
            ->find(TestEntity::class, new Equal(['field', 'value']))
            ->getSQL();
        $this->assertEquals("SELECT * FROM `some_database`.`user` WHERE 'field'='value'", $result);
    }

    /**
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::by
     * @throws Exception
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function testInjectionResistance(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'=''' OR 1=1 --value'''";
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', "' OR 1=1 --value'")
            ->getSQL();
        $this->assertEquals($expectedSimpleQuery, $result);
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='\" OR 1=1 --value\"'";
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', '" OR 1=1 --value"')
            ->getSQL();
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    /**
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::limit
     * @throws Exception
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function testLimitQueryBuilding(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' LIMIT 11";
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value')
            ->limit(11)
            ->getSQL();
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    /**
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::limit
     * @throws Exception
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function testLimitOffsetQueryBuilding(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' LIMIT 11 OFFSET 4";
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value')
            ->limit(11, 4)
            ->getSQL();
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    /**
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::by
     * @throws Exception
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function testQueryBuilding(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' AND 'another_field'='another_value'";
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value')
            ->by('another_field', 'another_value')
            ->getSQL();
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    /**
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::by
     * @throws Exception
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function testQueryBuilderOr(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' OR 'another_field'='another_value'";
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value', Operators::OR)
            ->by('another_field', 'another_value', Operators::OR)
            ->getSQL();
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    /**
     * @covers \DataMapper\QueryBuilder\Statements\WhereTrait::by
     * @throws Exception
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function testQueryBuilderXor(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' XOR 'another_field'='another_value'";
        $result = $this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value', Operators::OR)
            ->by('another_field', 'another_value', Operators::XOR)
            ->getSQL();
        $this->assertEquals($expectedSimpleQuery, $result);
    }
}
