<?php

namespace Pioneers\ClickHouse\Schema;

class Settings
{
    private array $list = [];

    public function __construct(?array $list = null)
    {
        if ($list !== null) {
            $this->list = $list;
        }
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function allowNullableKey(): self
    {
        $this->list['allow_nullable_key'] = 1;
        return $this;
    }
}
