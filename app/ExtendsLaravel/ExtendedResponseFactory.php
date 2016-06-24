<?php

namespace App\ExtendsLaravel;

use Illuminate\Routing\ResponseFactory;

class ExtendedResponseFactory extends ResponseFactory
{
    public function json($data = [], $status = 200, array $headers = [], $options = JSON_UNESCAPED_UNICODE)
    {
        return parent::json($data, $status, $headers, $options);
    }
}