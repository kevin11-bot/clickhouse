<?php

namespace Pioneers\ClickHouse\Schema;

use Pioneers\ClickHouse\Enum\ColumnFloatEnum;
use Pioneers\ClickHouse\Enum\ColumnIntegerEnum;
use Pioneers\ClickHouse\Enum\ColumnUnsignIntegerEnum;
use Pioneers\ClickHouse\Enum\DateTimeEnum;
use Pioneers\ClickHouse\Schema\Column\BooleanDefinition;
use Pioneers\ClickHouse\Schema\Column\DateTimeDefinition;
use Pioneers\ClickHouse\Schema\Column\DecimalDefinition;
use Pioneers\ClickHouse\Schema\Column\FixedStringDefinition;
use Pioneers\ClickHouse\Schema\Column\FloatDefinition;
use Pioneers\ClickHouse\Schema\Column\IntegerDefinition;
use Pioneers\ClickHouse\Schema\Column\StringDefinition;
use Pioneers\ClickHouse\Schema\Column\UnsignIntegerDefinition;

class ColumnBlueprint
{
    /* @var ColumnDefinition[] */
    private array $list = [];

    public function getList(): array
    {
        return $this->list;
    }

    public function merge(ColumnBlueprint $blueprint): void
    {
        $this->list = array_merge($this->list, $blueprint->getList());
    }

    /**
     * @template T of ColumnDefinition
     */
    private function addColumn(ColumnDefinition $column): ColumnDefinition
    {
        $this->list[] = $column;

        return $column;
    }

    // String
    public function string(string $name): StringDefinition
    {
        return $this->addColumn(new StringDefinition($name));
    }

    public function fixedString(string $name, int $length = 255): FixedStringDefinition
    {
        return $this->addColumn(new FixedStringDefinition($name, $length));
    }

    // Integer
    public function int8(string $name): IntegerDefinition
    {
        return $this->addColumn(new IntegerDefinition(ColumnIntegerEnum::Int8, $name));
    }

    public function int16(string $name): IntegerDefinition
    {
        return $this->addColumn(new IntegerDefinition(ColumnIntegerEnum::Int16, $name));
    }

    public function int32(string $name): IntegerDefinition
    {
        return $this->addColumn(new IntegerDefinition(ColumnIntegerEnum::Int32, $name));
    }

    public function int64(string $name): IntegerDefinition
    {
        return $this->addColumn(new IntegerDefinition(ColumnIntegerEnum::Int64, $name));
    }

    public function int128(string $name): IntegerDefinition
    {
        return $this->addColumn(new IntegerDefinition(ColumnIntegerEnum::Int128, $name));
    }

    public function int256(string $name): IntegerDefinition
    {
        return $this->addColumn(new IntegerDefinition(ColumnIntegerEnum::Int256, $name));
    }

    // UInteger
    public function uint8(string $name): UnsignIntegerDefinition
    {
        return $this->addColumn(new UnsignIntegerDefinition(ColumnUnsignIntegerEnum::UInt8, $name));
    }

    public function uint16(string $name): UnsignIntegerDefinition
    {
        return $this->addColumn(new UnsignIntegerDefinition(ColumnUnsignIntegerEnum::UInt16, $name));
    }

    public function uint32(string $name): UnsignIntegerDefinition
    {
        return $this->addColumn(new UnsignIntegerDefinition(ColumnUnsignIntegerEnum::UInt32, $name));
    }

    public function uint64(string $name): UnsignIntegerDefinition
    {
        return $this->addColumn(new UnsignIntegerDefinition(ColumnUnsignIntegerEnum::UInt64, $name));
    }

    public function uint128(string $name): UnsignIntegerDefinition
    {
        return $this->addColumn(new UnsignIntegerDefinition(ColumnUnsignIntegerEnum::UInt128, $name));
    }

    public function uint256(string $name): UnsignIntegerDefinition
    {
        return $this->addColumn(new UnsignIntegerDefinition(ColumnUnsignIntegerEnum::UInt256, $name));
    }

    // Floats
    public function float32(string $name): FloatDefinition
    {
        return $this->addColumn(new FloatDefinition(ColumnFloatEnum::Float32, $name));
    }

    public function float64(string $name): FloatDefinition
    {
        return $this->addColumn(new FloatDefinition(ColumnFloatEnum::Float64, $name));
    }

    public function bfloat16(string $name): FloatDefinition
    {
        return $this->addColumn(new FloatDefinition(ColumnFloatEnum::BFloat16, $name));
    }

    public function decimal(string $name, int $total = 10, int $scale = 2): DecimalDefinition
    {
        return $this->addColumn(new DecimalDefinition($name, $total, $scale));
    }

    public function boolean(string $name): BooleanDefinition
    {
        return $this->addColumn(new BooleanDefinition($name));
    }

    public function dateTime(string $name): DateTimeDefinition
    {
        return $this->addColumn(new DateTimeDefinition(DateTimeEnum::DateTime, $name));
    }

    public function dateTime64(string $name, int $precision = 9): DateTimeDefinition
    {
        $options = ['precision' => $precision];

        return $this->addColumn(new DateTimeDefinition(DateTimeEnum::DateTime64, $name, $options));
    }
}
