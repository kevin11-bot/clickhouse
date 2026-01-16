<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Pioneers\ClickHouse\Schema\ColumnDefinition;
use Pioneers\ClickHouse\Trait\NullableTrait;

class StringDefinition extends ColumnDefinition
{
    use NullableTrait;

    public function __construct(string $name)
    {
        parent::__construct('String', $name);
    }
}
