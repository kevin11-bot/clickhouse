<?php

namespace Pioneers\ClickHouse\Schema;

use Pioneers\ClickHouse\Builder\Builder;

readonly class Grammar
{
    public static function createTable(
        Schema $schema,
    ): string {
        return sprintf(
            'CREATE TABLE IF NOT EXISTS %s
            (
                %s
            )
                %s
                %s
                %s
                %s
            ;',
            $schema->tableName,
            self::columns($schema->column),
            self::engine($schema->engine),
            self::grammarOrderByList($schema->order),
            self::settings($schema->settings),
            self::partition($schema->partition)
        );
    }

    public static function partition(Partition $partition): string
    {
        $value = $partition->getValue();

        if ($value) {
            return sprintf('PARTITION BY %s', $value);
        }

        return '';
    }

    public static function columns(ColumnBlueprint $blueprint): string
    {
        $a = '';
        $columns = $blueprint->getList();

        foreach ($columns as $i => $column) {
            $a .= $column->grammar($i === count($columns) - 1)."\n";
        }

        return $a;
    }

    public static function engine(EngineBlueprint $engineBlueprint): string
    {
        $options = $engineBlueprint->getDefinition()->getOptions();

        $replaceColumn = $options['replace_column'] ?? '';

        return sprintf(
            'ENGINE = %s(%s)',
            $engineBlueprint->getDefinition()->getType(),
            $replaceColumn
        );
    }

    public static function settings(Settings $settings): string
    {
        $list = $settings->getList();

        if (empty($list)) {
            return '';
        }

        foreach ($list as $key => $value) {
            $list[$key] = sprintf('%s = %s', $key, $value);
        }

        return sprintf('SETTINGS %s', implode(', ', $list));
    }

    public static function grammarOrderByList(Order $order): string
    {
        return sprintf('ORDER BY (%s)', implode(', ', $order->getList()));
    }

    public static function getQuery(Builder $query): string
    {
        $bindings = $query->getBindings();

        return sprintf(
            '%s %s %s %s %s %s %s %s %s',
            self::select(
                $query->getTableName(),
                $bindings['select'],
                $query->getDistinctColumn(),
                $query->getIsFinal()
            ),
            self::where($bindings['where'], $query),
            self::groupBy($bindings['groupBy']),
            self::having($bindings['having']),
            self::orderBy($bindings['orderBy']),
            self::limit($bindings['limit']),
            self::offset($bindings['offset']),
            $bindings['withTotals'] ? 'WITH TOTALS' : '',
            $query->getSubQuery() ? ' ' : ' ;'
        );
    }

    public static function getQueryCount(Builder $query): string
    {
        $bindings = $query->getBindings();

        return sprintf(
            'SELECT COUNT(*) as count from %s %s %s %s %s %s %s',
            $query->getTableName(),
            self::where($bindings['where'], $query),
            self::groupBy($bindings['groupBy']),
            self::having($bindings['having']),
            self::limit($bindings['limit']),
            self::offset($bindings['offset']),
            $query->getSubQuery() ? ' ' : ' ;'
        );
    }

    public static function having(?string $having): string
    {
        if (! $having) {
            return '';
        }

        return sprintf('HAVING %s', $having);
    }

    public static function updateQuery(Builder $query): string
    {
        $bindings = $query->getBindings();

        return sprintf(
            '%s %s %s;',
            self::alter($query->getTableName()),
            self::update($bindings['update']),
            self::where($bindings['where'], $query)
        );
    }

    public static function groupBy(array $groupBy): string
    {
        if (count($groupBy) === 0) {
            return '';
        }

        return sprintf('GROUP BY %s', implode(', ', $groupBy));
    }

    public static function orderBy(array $orderBy): string
    {
        if (count($orderBy) === 0) {
            return '';
        }

        $q = 'ORDER BY ';
        $j = 0;

        foreach ($orderBy as $key => $value) {
            $isLast = $j++ === count($orderBy) - 1;
            $q .= sprintf(
                '%s %s %s',
                $key,
                $value === 'asc' ? '' : 'desc',
                $isLast ? '' : ', '
            );
        }

        return $q;
    }

    public static function limit(?int $limit): string
    {
        if ($limit === null) {
            return '';
        }

        return sprintf('LIMIT %s', $limit);
    }

    public static function offset(?int $offset): string
    {
        if ($offset === null) {
            return '';
        }

        return sprintf('OFFSET %s', $offset);
    }

    public static function update(array $data): string
    {
        $u = 'UPDATE ';

        foreach ($data as $column => $value) {
            $value = gettype($value) === 'string' ? "'$value'" : $value;

            if (gettype($value) === 'boolean') {
                $value = $value ? '1' : '0';
            }

            $u .= sprintf('%s = %s, ', $column, $value);
        }

        return rtrim($u, ', ');
    }

    public static function alter(string $from): string
    {
        return sprintf(
            'ALTER TABLE %s',
            $from
        );
    }

    public static function select(string $from, array $selectBindings, ?string $distinctColumn = null, ?bool $isFinal = false): string
    {
        if ($distinctColumn) {
            return sprintf('SELECT DISTINCT %s FROM %s', $distinctColumn, $from);
        }

        $select = '*';

        if (count($selectBindings) > 0) {
            $select = '';
            $j = 0;

            foreach ($selectBindings as $key => $value) {
                $isLast = $j++ === count($selectBindings) - 1;

                if (gettype($key) === 'integer') {
                    if (gettype($value) === 'string') {
                        $select .= sprintf('%s', $value);
                    } else {
                        $select .= $value->getGrammar();
                    }
                } else {
                    $select .= sprintf('%s as %s', $key, $value);
                }

                $select .= $isLast ? '' : ', ';
            }
        }

        return sprintf('SELECT %s FROM %s %s', $select, $from, $isFinal ? 'FINAL' : '');
    }

    public static function where(array $whereBindings, Builder $builder, bool $withWhere = true): string
    {
        if (count($whereBindings) === 0) {
            return '';
        }

        $g = $withWhere ? 'WHERE ' : '';

        foreach ($whereBindings as $i => $where) {
            $isFirst = $i === 0;

            [$column, $operator, $value, $boolean] = array_values($where);

            if ($column === '') {
                $g .= sprintf('%s %s ',
                    $isFirst ? '' : $boolean,
                    $value
                );
                continue;
            }

            if (is_array($value)) {
                $quoted_elements = array_map(function ($item) {
                    return "'".$item."'";
                }, $value);

                $g .= sprintf('%s %s %s (%s) ',
                    $isFirst ? '' : $boolean,
                    $column,
                    $operator,
                    implode(', ', $quoted_elements),
                );

            } elseif ($value instanceof \Closure) {
                $q = new Builder($builder->getTableName());
                $q->setSubQuery(true);

                $value($q);

                $g .= sprintf('%s %s %s (%s) ',
                    $isFirst ? '' : $boolean,
                    $column,
                    $operator,
                    $q->selectToSql(),
                );
            } else {
                $g .= sprintf('%s %s %s %s ',
                    $isFirst ? '' : $boolean,
                    $column,
                    $operator,
                    gettype($value) === 'string' ? "'$value'" : $value,
                );
            }
        }

        return $g;
    }
}
