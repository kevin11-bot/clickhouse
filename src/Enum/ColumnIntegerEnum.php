<?php

namespace Pioneers\ClickHouse\Enum;

enum ColumnIntegerEnum: string
{
    case Int8 = 'Int8';
    case Int16 = 'Int16';
    case Int32 = 'Int32';
    case Int64 = 'Int64';
    case Int128 = 'Int128';
    case Int256 = 'Int256';
}
