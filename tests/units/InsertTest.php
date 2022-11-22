<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use PDO;
use PHPUnit\Framework\TestCase;
use Test\TestEntity;

class InsertTest extends TestCase
{
    /**
     * @var DataMapper
     */
    private DataMapper $dataMapper;

    public function setUp(): void
    {
        $this->dataMapper = new DataMapper($this->createMock(PDO::class));
    }

    public function testSimpleQueryBuilder(): void
    {
        $entity = new TestEntity();
        $entity->id = 1337;
        $entity->data = [];
        $entity->email = 'some_address@some_domain.tld';
        $entity->createdAt = new \DateTime('2012-12-21 12:12:12');
        $entity->updatedAt = new \DateTime('2012-12-21 12:12:12');
        $entity->from = 'dsfsdf';
        $entity->scores = 0;
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value'";
        $result = (string)$this->dataMapper->store($entity);
        $this->assertEquals($expectedSimpleQuery, $result);
    }
}
