<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Pioneers\ClickHouse\Enum\ColumnIntegerEnum;
use Pioneers\ClickHouse\Schema\ColumnDefinition;

class IntegerDefinition extends ColumnDefinition
{
    public function __construct(ColumnIntegerEnum $type, string $name)
    {
        parent::__construct($type->value, $name);
    }
}
