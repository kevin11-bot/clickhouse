<?php

namespace Pioneers\ClickHouse\Enum;

enum ColumnFloatEnum: string
{
    case Float32 = 'Float32';
    case Float64 = 'Float64';
    case BFloat16 = 'BFloat16';
}
