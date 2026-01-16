<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Pioneers\ClickHouse\Schema\ColumnDefinition;
use Pioneers\ClickHouse\Trait\NullableTrait;

class BooleanDefinition extends ColumnDefinition
{
    use NullableTrait;

    public function __construct(string $name)
    {
        parent::__construct('Bool', $name);
    }
}
