<?php

namespace Pioneers\ClickHouse\Schema\Column;

use Exception;
use Pioneers\ClickHouse\Enum\DateTimeEnum;
use Pioneers\ClickHouse\Schema\ColumnDefinition;
use Pioneers\ClickHouse\Trait\NullableTrait;

class DateTimeDefinition extends ColumnDefinition
{
    use NullableTrait;

    public function __construct(DateTimeEnum $type, string $name, array $options = [])
    {
        parent::__construct($type->value, $name, $options);
    }

    /**
     * @throws Exception
     */
    public function baseGrammar(): string
    {
        if ($this->type === DateTimeEnum::DateTime64->value) {

            if (!isset($this->options['precision'])) {
                throw new Exception('Precision is required for Decimal64 type.');
            }

            return sprintf(
                '%s(%s)',
                $this->type,
                $this->options['precision'],
            );
        }

        return parent::baseGrammar();
    }
}
