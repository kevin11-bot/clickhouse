<?php

namespace Pioneers\ClickHouse\Relationship;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BelongsTo
{
    protected $related;

    protected string $key = 'id';

    protected string $foreignKey;

    protected string $name;

    public function __construct($related, string $name, $foreignKey = null)
    {
        $this->related = $related;
        $this->name = $name;

        if ($foreignKey === null) {
            $foreignKey = Str::lower(Str::snake($name)).'_id';
        }

        $this->foreignKey = $foreignKey;
    }

    public function fillData(Collection $rows, array $relation): Collection
    {
        $foreignIdList = $rows->pluck($this->foreignKey)->unique()->values();

        if ($relation['callback']) {
            $q = $this->related::query()->whereIn($this->key, $foreignIdList);
            $relation['callback']($q);
            $relationItems = $q->get()->keyBy($this->key);
        } else {
            $relationItems = $this->related::query()->whereIn($this->key, $foreignIdList)
                ->get($relation['columns'])
                ->keyBy($this->key);

        }

        return $rows->map(function ($row) use ($relationItems) {
            $row[$this->name] = $relationItems[$row[$this->foreignKey]] ?? null;

            return $row;
        });
    }
}
