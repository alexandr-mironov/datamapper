<?php

declare(strict_types=1);

namespace Test;

use DateTime;
use DataMapper\Attributes\{Column, Table};

#[Table(name: 'user', schema: 'some_database')]
class TestEntity
{
    /** @var int $id primary key */
    #[Column(name: 'id', type: Column::INTEGER, options: [Column::AUTOINCREMENT, Column::PRIMARY_KEY, 'length' => 11])]
    public int $id;

    /** @var string $username username */
    #[Column(name: 'username', type: Column::STRING, options: ['length' => 255])]
    public string $from;

    /** @var string $email user email */
    #[Column(name: 'email', type: Column::STRING, options: ['length' => 255])]
    public string $email;

    /** @var DateTime $createdAt user creation datetime */
    #[Column(name: 'created_at', type: Column::DATETIME)]
    public DateTime $createdAt;

    /** @var DateTime $updatedAt last user update datetime */
    #[Column(name: 'updated_at', type: Column::DATETIME)]
    public DateTime $updatedAt;

    /** @var int $scores current user scores*/
    #[Column(name: 'scores', type: Column::INTEGER, options: ['length' => 11])]
    public int $scores;

    /** @var array $data specific user data*/
    #[Column(name: 'data', type: Column::JSONB)]
    public array $data = [];
}
