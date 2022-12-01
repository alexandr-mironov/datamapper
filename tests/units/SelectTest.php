<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use DataMapper\QueryBuilder\Operators;
use DataMapper\QueryBuilder\QueryBuilder;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Test\TestEntity;

class SelectTest extends TestCase
{
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
    }

    public function testAttributeTable(): void
    {
        $dataMapper = $this->createMock(DataMapper::class);
        $table = $dataMapper->getTable(new ReflectionClass(TestEntity::class));
        $this->assertEquals('`some_database`', $table->getName());
    }

    public function testSimpleQueryBuilding(): void
    {
        $dataMapper = $this->createMock(DataMapper::class);
        $result = (string)$dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value');
        $this->assertEquals("SELECT * FROM `some_database`.`user` WHERE 'field'='value'", $result);
    }

    public function testInjectionResistance(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'=''' OR 1=1 --value'''";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', "' OR 1=1 --value'");
        $this->assertEquals($expectedSimpleQuery, $result);
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='\" OR 1=1 --value\"'";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', '" OR 1=1 --value"');
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    public function testLimitQueryBuilding(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' LIMIT 11";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value')
            ->limit(11);
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    public function testLimitOffsetQueryBuilding(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' LIMIT 11 OFFSET 4";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value')
            ->limit(11, 4);
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    public function testQueryBuilding(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' AND 'another_field'='another_value'";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value')
            ->by('another_field', 'another_value');
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    public function testQueryBuilderOr(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' OR 'another_field'='another_value'";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value', Operators::OR)
            ->by('another_field', 'another_value', Operators::OR);
        $this->assertEquals($expectedSimpleQuery, $result);
    }

    public function testQueryBuilderXor(): void
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value' XOR 'another_field'='another_value'";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value', Operators::OR)
            ->by('another_field', 'another_value', Operators::XOR);
        $this->assertEquals($expectedSimpleQuery, $result);
    }
}
