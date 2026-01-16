<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Exception;
use Pioneers\ClickHouse\Schema\ColumnDefinition;
use Pioneers\ClickHouse\Trait\NullableTrait;

class FixedStringDefinition extends ColumnDefinition
{
    use NullableTrait;

    public function __construct(string $name, int $length)
    {
        parent::__construct('FixedString', $name, [
            'length' => $length,
        ]);
    }

    /**
     * @throws Exception
     */
    public function baseGrammar(): string
    {
        if (! isset($this->options['length'])) {
            throw new Exception('Length is required for FixedString type.');
        }

        return sprintf('%s(%s)', $this->type, $this->options['length']);
    }
}
