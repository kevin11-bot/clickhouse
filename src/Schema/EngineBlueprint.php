<?php

namespace Pioneers\ClickHouse\Schema;

class EngineBlueprint
{
    private EngineDefinition $engine;

    public function __construct(?EngineDefinition $engine = null)
    {
        if ($engine === null) {
            $engine = EngineDefinition::default();
        }

        $this->engine = $engine;
    }

    public static function default(): self
    {
        return new self;
    }

    public function mergeTree(): self
    {
        $this->engine = EngineDefinition::mergeTree();

        return $this;
    }

    public function replicatedMergeTree(): self
    {
        $this->engine = EngineDefinition::replicatedMergeTree();

        return $this;
    }

    public function replacingMergeTree(string $column): self
    {
        $this->engine = EngineDefinition::replacingMergeTree($column);

        return $this;
    }

    public function getDefinition(): EngineDefinition
    {
        return $this->engine;
    }

    public function getType(): string
    {
        return $this->engine->getType();
    }
}
