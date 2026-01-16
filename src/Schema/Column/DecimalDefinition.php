<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Exception;
use Pioneers\ClickHouse\Schema\ColumnDefinition;
use Pioneers\ClickHouse\Trait\NullableTrait;

class DecimalDefinition extends ColumnDefinition
{
    use NullableTrait;

    public function __construct(string $name, int $total, int $scale)
    {
        parent::__construct('Decimal', $name, [
            'total' => $total,
            'scale' => $scale,
        ]);
    }

    /**
     * @throws Exception
     */
    public function baseGrammar(): string
    {
        if (! isset($this->options['total'], $this->options['scale'])) {
            throw new Exception('Total and Scale is required for Decimal type.');
        }

        return sprintf(
            '%s(%s,%s)',
            $this->type,
            $this->options['total'],
            $this->options['scale'],
        );
    }
}
