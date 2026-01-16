<?php

namespace Pioneers\ClickHouse\Builder;

use BadMethodCallException;
use ClickHouseDB\Statement;
use DateTimeInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Pioneers\ClickHouse\Connection;
use Pioneers\ClickHouse\Model;
use Pioneers\ClickHouse\Schema\Grammar;
use Throwable;

class Builder
{
    private Connection $connection;

    private ?string $tableName = null;

    private ?string $distinctColumn = null;

    private bool $isSubQuery = false;

    private bool $isFinal = false;

    public ?Model $model = null;

    private array $relations = [];

    /**
     * The current query value bindings.
     *
     * @var array{
     *     where: list<mixed>,
     * }
     */
    private array $bindings = [
        'where' => [],
        'select' => [],
        'update' => [],
        'groupBy' => [],
        'orderBy' => [],
        'limit' => null,
        'offset' => null,
        'having' => null,
    ];

    public function __construct(?string $tableName = null, ?Model $model = null)
    {
        $this->tableName = $tableName;
        $this->connection = new Connection;
        $this->model = $model;
    }

    public function getSubQuery(): bool
    {
        return $this->isSubQuery;
    }

    public function setSubQuery(bool $flag): self
    {
        $this->isSubQuery = $flag;

        return $this;
    }

    public function getDistinctColumn(): ?string
    {
        return $this->distinctColumn;
    }

    public function getIsFinal(): bool
    {
        return $this->isFinal;
    }

    public function insert(array $data, array $columns = []): Statement
    {
        return $this->connection->client->insert($this->tableName, $data, $columns);
    }

    public function select(array $columns): self
    {
        $this->bindings['select'] = $columns;

        return $this;
    }

    public function distinct(string $column): self
    {
        $this->distinctColumn = $column;

        return $this;
    }

    public function from(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function whereRaw(string $value, $boolean = 'AND'): self
    {
        $this->bindings['where'][] = ['', '', $value, $boolean];

        return $this;
    }

    public function where(string $column, $operator = null, $value = null, $boolean = 'AND'): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->bindings['where'][] = compact('column', 'operator', 'value', 'boolean');

        return $this;
    }

    public function whereIn(string $column, array|callable $values, $boolean = 'AND'): self
    {
        $operator = 'IN';

        $this->bindings['where'][] = compact('column', 'operator', 'values', 'boolean');

        return $this;
    }

    public function whereNotIn(string $column, array|callable $values, $boolean = 'AND'): self
    {
        $operator = 'NOT IN';

        $this->bindings['where'][] = compact('column', 'operator', 'values', 'boolean');

        return $this;
    }

    public function final(bool $final = true): self
    {
        $this->isFinal = $final;

        return $this;
    }

    public function whereDate(string $column, $operator = null, $value = null, $boolean = 'AND'): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y-m-d');
        }

        return $this->where($column, $operator, $value, $boolean);
    }

    public function limit(int $limit = 15): self
    {
        $this->bindings['limit'] = $limit;

        return $this;
    }

    public function offset(int $offset = 0): self
    {
        $this->bindings['offset'] = $offset;

        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->bindings['groupBy'] = $columns;

        return $this;
    }

    public function havingRaw(string $value): self
    {
        $this->bindings['having'] = $value;

        return $this;
    }

    public function orderBy(string $column, string $dir = 'asc'): self
    {
        $this->bindings['orderBy'][$column] = $dir;

        return $this;
    }

    public function selectToSql(): string
    {
        return Grammar::getQuery($this);
    }

    public function get(): array|Collection
    {
        $rows = $this->connection->client->select(
            $this->selectToSql()
        )->rows();

        if ($this->model && count($this->relations) > 0) {
            return $this->fillRelations($rows);
        }

        return $rows;
    }

    public function count(): int
    {
        $rows = $this->connection->client->select(
            Grammar::getQueryCount($this)
        )->rows();

        return $rows[0]['count'];
    }

    public function fillRelations(array $rows): Collection
    {
        $rows = collect($rows);

        foreach ($this->relations as $relation) {
            $rows = $this->model->{$relation['relationName']}()->fillData($rows, $relation);
        }

        return $rows;
    }

    public function update(array $data)
    {
        $this->bindings['update'] = $data;

        return $this->connection->client->write(
            Grammar::updateQuery($this)
        );
    }

    public function getWhereGrammar(bool $withWhere = true): string
    {
        return Grammar::where($this->bindings['where'], $this, $withWhere);
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @throws Throwable
     */
    public function __call(string $method, array $parameters): static
    {
        throw_if($this->model === null, new \Exception('Model is not set'));

        $localScopeMethod = $this->model->getLocalScopeMethods();

        if (isset($localScopeMethod[$method])) {
            $method = $localScopeMethod[$method];
            $this->model->{$method->name}($this, ...$parameters);

            return $this;
        }

        $name = $this->model::class;

        throw new BadMethodCallException("Method {$method} does not exist in {$name}.");
    }

    public function with(array $relations = []): self
    {

        foreach ($relations as $key => $relation) {
            if (gettype($key) === 'integer') {
                $modelAndColumns = explode(':', $relation);
                $columns = ['*'];
                $callback = null;

                if (count($modelAndColumns) > 1) {
                    $relation = $modelAndColumns[0];
                    $columns = explode(',', $modelAndColumns[1]);
                }
            } else {
                $callback = $relation;
                $relation = $key;
                $columns = ['*'];
            }

            $this->relations[] = [
                'relationName' => $relation,
                'columns' => $columns,
                'callback' => $callback,
            ];
        }

        return $this;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $count = $this->count();

        $page = request()->get('page', 1);

        if (gettype($page) !== 'string') {
            $page = 1;
        }

        $limit = $perPage;
        $offset = ($page - 1) * $limit;

        $this->limit($limit);
        $this->offset($offset);

        $data = $this->get();

        return (new LengthAwarePaginator($data, $count, $limit, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]))->withQueryString()->onEachSide(1);
    }
}
