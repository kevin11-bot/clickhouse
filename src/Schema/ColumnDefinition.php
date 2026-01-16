<?php

namespace Pioneers\ClickHouse\Schema;

class ColumnDefinition
{
    public function __construct(
        protected readonly string $type,
        protected readonly string $name,
        protected array $options = []
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function default(string $value): self
    {
        $this->options['default'] = $value;

        return $this;
    }

    public function grammar($isLast = false): string
    {
        $base = $this->baseGrammar();
        $base = $this->nullableGrammar($base);
        $base = $this->defaultGrammar($base);

        $base = sprintf('%s %s', $this->name, $base);
        return $this->lastColumnGrammar($base, $isLast);
    }

    public function defaultGrammar(string $base): string
    {
        if (isset($this->options['default'])) {
            return sprintf('%s DEFAULT %s', $base, $this->options['default']);
        }

        return $base;
    }

    public function baseGrammar(): string
    {
        return sprintf('%s', $this->type);
    }

    public function nullableGrammar(string $base): string
    {
        if (isset($this->options['nullable']) && $this->options['nullable']) {
            return 'Nullable('.$base.')';
        }

        return $base;
    }

    public function lastColumnGrammar(string $base, bool $isLast): string
    {
        if (! $isLast) {
            $base .= ',';
        }

        return $base;
    }
}
