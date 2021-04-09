<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;

trait CompositeKeysTrait {
    /**
     * Set the keys for a save update query.
     *
     * @param  Builder $query
     * @return Builder
     * @throws Exception
     */
    protected function setKeysForSaveQuery(Builder $query) {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $key) {
            $query->where($key, '=', $this->getKeyForSaveQuery($key));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query
     * 
     * @param mixed $key
     * @return mixed
     */
    protected function getKeyForSaveQuery($key = null) {
        if (is_null($key)) {
            $key = $this->getKeyName();
        }

        if (isset($this->original[$key])) {
            return $this->original[$key];
        }

        return $this->getAttribute($key);
    }
}