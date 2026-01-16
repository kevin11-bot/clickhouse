<?php

namespace Pioneers\ClickHouse;

use Pioneers\ClickHouse\Builder\Builder;
use Pioneers\ClickHouse\Builder\BuilderCount;
use Pioneers\ClickHouse\Builder\BuilderSum;

readonly class DB
{
    public static function table(string $tableName, ?Model $model = null): Builder
    {
        return new Builder($tableName, $model);
    }

    public static function count(string $column = '*', ?string $alias = null): BuilderCount
    {
        return new BuilderCount($column, $alias);
    }

    public static function sum(string $column, ?string $alias = null): BuilderSum
    {
        return new BuilderSum($column, $alias);
    }

    public static function raw(string $sql): string
    {
        return $sql;
    }
}
