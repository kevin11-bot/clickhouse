<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Pioneers\ClickHouse\Enum\ColumnUnsignIntegerEnum;
use Pioneers\ClickHouse\Schema\ColumnDefinition;
use Pioneers\ClickHouse\Trait\NullableTrait;

class UnsignIntegerDefinition extends ColumnDefinition
{
    use NullableTrait;

    public function __construct(ColumnUnsignIntegerEnum $type, string $name)
    {
        parent::__construct($type->value, $name);
    }
}
