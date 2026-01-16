<?php

namespace Pioneers\ClickHouse\Trait;

trait NullableTrait
{
    public function nullable(): self
    {
        $this->options['nullable'] = true;

        return $this;
    }
}
