<?php

namespace Pioneers\ClickHouse\Builder;

class BuilderSum
{
    private Builder $query;

    private bool $hasWhere = false;

    public function __construct(
        private string $column,
        private ?string $alias = null
    ) {
        $this->query = new Builder;
    }

    public function when(string $column, string|int $value): self
    {
        $this->query->where($column, $value);

        $this->hasWhere = true;

        return $this;
    }

    public function alias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getGrammar(): string
    {
        if ($this->hasWhere) {
            $q = sprintf('sumIf(%s, %s)', $this->column, $this->query->getWhereGrammar(false));
        } else {
            $q = sprintf('sum(%s)', $this->column);
        }

        if ($this->alias) {
            return sprintf('%s as %s', $q, $this->alias);
        }

        return $q;
    }
}
