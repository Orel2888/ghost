<?php

namespace App\Ghost\Domains\Miner\Entities;

class Entity
{
    public function __get($name)
    {
        if ($this->collection->has($name)) {
            return $this->collection->get($name);
        }
    }
}