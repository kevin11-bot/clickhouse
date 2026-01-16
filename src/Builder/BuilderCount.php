<?php

namespace Pioneers\ClickHouse\Builder;

class BuilderCount
{
    public function __construct(
        private string $column,
        private ?string $alias = null
    ) {}

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function as(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getGrammar(): string
    {
        return sprintf('count(%s) as %s', $this->column, $this->alias ?? 'count');
    }
}
