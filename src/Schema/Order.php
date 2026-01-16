<?php

namespace Pioneers\ClickHouse\Schema;

class Order
{
    private array $list = [];

    public function __construct(?array $list = null)
    {
        if ($list !== null) {
            $this->list = $list;
        }
    }

    public function by(string ...$columns): self
    {
        $this->list = $columns;
        return $this;
    }

    public function add(string $column): self
    {
        $this->list[] = $column;

        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }
}
