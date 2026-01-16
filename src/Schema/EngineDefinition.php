<?php

namespace Pioneers\ClickHouse\Schema;

use Pioneers\ClickHouse\Enum\EngineTypeEnum;

readonly class EngineDefinition
{
    public function __construct(
        private EngineTypeEnum $type,
        private array $options = []
    ) {}

    public function getType(): string
    {
        return $this->type->value;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public static function default(): self
    {
        return new self(EngineTypeEnum::MERGE_TREE);
    }

    public static function mergeTree(): self
    {
        return new self(EngineTypeEnum::MERGE_TREE);
    }

    public static function replicatedMergeTree(): self
    {
        return new self(EngineTypeEnum::REPLICATED_MERGE_TREE);
    }

    public static function replacingMergeTree(string $column): self
    {
        return new self(EngineTypeEnum::REPLACING_MERGE_TREE, [
            'replace_column' => $column,
        ]);
    }
}
