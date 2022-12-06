<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use DataMapper\Entity\Table;
use DataMapper\Exceptions\Exception;
use DataMapper\QueryBuilder\QueryBuilder;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Test\TestEntity;

class DataMapperTest extends TestCase
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
            'SELECT * FROM `some_database`.`user`',
            $this->dataMapper->find(TestEntity::class)->getSQL()
        );
    }
}
