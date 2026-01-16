<?php

namespace Pioneers\ClickHouse\Schema;

use Exception;
use Pioneers\ClickHouse\Connection;

readonly class Schema
{
    public EngineBlueprint $engine;

    public Order $order;

    public Partition $partition;

    public ColumnBlueprint $column;

    public string $tableName;

    public Settings $settings;

    public function __construct(string $tableName)
    {
        $this->engine = EngineBlueprint::default();
        $this->tableName = $tableName;
        $this->order = new Order;
        $this->partition = new Partition;
        $this->column = new ColumnBlueprint;
        $this->settings = new Settings;
    }

    /**
     * @throws Exception
     */
    public static function create(string $tableName, callable $callback): void
    {
        $blueprint = new ColumnBlueprint;
        $schema = new Schema($tableName);

        $callback($blueprint, $schema);

        $schema->column->merge($blueprint);

        $schema->beforeQueryRun();

        Connection::client()->write(Grammar::createTable($schema));
    }

    /**
     * @throws Exception
     */
    public function beforeQueryRun(): void
    {
        if (empty($this->tableName)) {
            throw new Exception('Make sure to set the table name');
        }

        if (empty($this->column->getList())) {
            throw new Exception('Make sure to set the columns of the table');
        }

        if (empty($this->order->getList())) {
            throw new Exception('Make sure to set the order of the table');
        }
    }
}
