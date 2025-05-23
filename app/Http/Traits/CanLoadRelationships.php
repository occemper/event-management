<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanLoadRelationships
{
    public function loadRelationships(
        Model|Builder|QueryBuilder|HasMany $for,
        ?array $relations = null
    ): Model|Builder|QueryBuilder|HasMany {
        $relations = $relations ?? $this->relations ?? [];


        foreach ($relations as $relation) {

            $for->when(
                $this->shouldIncludRelation($relation),
                fn($q) => $for instanceof Model ? $for->load($relation) : $q->with($relation)
            );
        }

        return $for;
    }

    protected function shouldIncludRelation(string $relation): bool
    {
        $include = request()->query('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));



        return in_array($relation, $relations);
    }
}
