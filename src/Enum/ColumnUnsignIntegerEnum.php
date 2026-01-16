<?php

namespace Pioneers\ClickHouse\Enum;

enum ColumnUnsignIntegerEnum: string
{
    case UInt8 = 'UInt8';
    case UInt16 = 'UInt16';
    case UInt32 = 'UInt32';
    case UInt64 = 'UInt64';
    case UInt128 = 'UInt128';
    case UInt256 = 'UInt256';
}
