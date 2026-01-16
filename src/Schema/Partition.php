<?php

namespace Pioneers\ClickHouse\Schema;

class Partition
{
    private ?string $value = null;

    public function __construct(?string $value = null)
    {
        $this->value = $value;
    }

    public function by(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function byYear(string $column): self
    {
        $this->value = "toYear($column)";
        return $this;
    }

    public function byMonth(string $column): self
    {
        $this->value = "toYYYYMM($column)";
        return $this;
    }

    public function byDay(string $column): self
    {
        $this->value = "toDate($column)";

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
