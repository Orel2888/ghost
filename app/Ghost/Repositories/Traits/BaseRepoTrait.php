<?php

namespace App\Ghost\Repositories\Traits;

use InvalidArgumentException;

trait BaseRepoTrait
{
    public function checkRequiredAttributesArray($attributes, $attributesRequired)
    {
        foreach ($attributesRequired as $key) {
            if (!isset($attributes[$key])) {
                throw new InvalidArgumentException("Attribute a {$key} is required");
            }
        }
    }
}