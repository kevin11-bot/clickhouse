<?php

namespace Pioneers\ClickHouse\Trait;

use Pioneers\ClickHouse\Relationship\BelongsTo;

trait HasRelationships
{
    /**
     * The loaded relationships for the model.
     */
    protected array $relations = [];

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TRelatedModel>  $related
     */
    public function belongsTo($related): BelongsTo
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $caller = $backtrace[1];
        $relationshipName = $caller['function'];

        return new BelongsTo(new $related, $relationshipName);
    }
}
