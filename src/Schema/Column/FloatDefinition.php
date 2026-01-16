<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Pioneers\ClickHouse\Enum\ColumnFloatEnum;
use Pioneers\ClickHouse\Schema\ColumnDefinition;

class FloatDefinition extends ColumnDefinition
{
    public function __construct(ColumnFloatEnum $type, string $name)
    {
        parent::__construct($type->value, $name);
    }
}
