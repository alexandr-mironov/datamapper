<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use DataMapper\Entity\Table;
use DataMapper\Exceptions\Exception;
use DataMapper\QueryBuilder\Exceptions\Exception as QueryBuilderException;
use DataMapper\QueryBuilder\Exceptions\UnsupportedException;
use DataMapper\QueryBuilder\QueryBuilder;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Test\TestEntity;

class DataMapperTest extends TestCase
{
    public const TEST_ENTITY_FIELD_SET = 'id as TestEntity.id, username as TestEntity.from, email as TestEntity.email, created_at as TestEntity.createdAt, updated_at as TestEntity.updatedAt, scores as TestEntity.scores, data as TestEntity.data';
    /**
     * @var DataMapper
     */
    private DataMapper $dataMapper;

    /**
     * @throws QueryBuilderException
     * @throws UnsupportedException
     */
    public function setUp(): void
    {
        $pdo = $this->createMock(PDO::class);
        $this->dataMapper = DataMapper::construct($pdo, QueryBuilder::SQL1999, false);
    }

    /**
     * @covers \DataMapper\DataMapper::find
     * @throws Exception
     * @throws ReflectionException
     */
    public function testFind()
    {
        $self = $this->dataMapper->find(TestEntity::class);
        $this->assertTrue($self instanceof DataMapper);
    }

    /**
     * @covers \DataMapper\DataMapper::getTable
     * @covers \DataMapper\Entity\Table::getName
     * @throws Exception
     */
    public function testGetTable()
    {
        $table = $this->dataMapper->getTable(new ReflectionClass(TestEntity::class));
        $this->assertTrue($table instanceof Table);
        $this->assertEquals('`some_database`.`user`', $table->getName());
    }

    /**
     * @covers \DataMapper\DataMapper::getSQL
     * @throws Exception
     * @throws ReflectionException
     */
    public function testGetSQL()
    {
        $this->assertEquals(
            "SELECT " . self::TEST_ENTITY_FIELD_SET . " FROM `some_database`.`user`",
            $this->dataMapper->find(TestEntity::class)->getSQL()
        );
    }
}
