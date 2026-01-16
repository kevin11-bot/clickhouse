<?php

namespace Pioneers\ClickHouse;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Pioneers\ClickHouse\Builder\Builder;
use Pioneers\ClickHouse\Trait\HasRelationships;
use ReflectionClass;
use ReflectionMethod;

abstract class Model
{
    use HasRelationships;

    abstract public function getTableName(): string;

    public function getLocalScopeMethods(): array
    {
        $classReflection = new ReflectionClass(static::class);
        $scopeMethods = [];

        $methods = $classReflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(Scope::class);

            if (! empty($attributes)) {
                $scopeName = lcfirst(str_replace('scope', '', $method->getName()));

                $scopeMethods[$scopeName] = $method;
            }
        }

        return $scopeMethods;
    }

    public static function query(): Builder
    {
        $model = new static;

        return DB::table($model->getTableName(), $model);
    }
}
