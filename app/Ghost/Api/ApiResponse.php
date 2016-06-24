<?php

namespace App\Ghost\Api;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class ApiResponse implements Jsonable, Arrayable, JsonSerializable
{
    /**
     * Errors array
     * @var array
     */
    private $errors = [];

    /**
     * Response a data
     * @var array
     */
    private $responseData = [];

    /**
     * Status a operation
     * @var bool
     */
    public $success = false;

    /**
     * Status code response
     * @var int
     */
    public $statusCode = 200;

    public function ok(array $data = [])
    {
        $this->success = true;

        $this->responseData = array_merge($this->responseData, ['status' => 'ok'], $data);

        return $this;
    }

    public function fail(array $data = [])
    {
        $this->responseData = array_merge($this->responseData, ['status' => 'fail'], $data);

        return $this;
    }

    public function error($error, $errorValue = null)
    {
        if (!is_null($errorValue)) {
            $error = [$error => $errorValue];
        }

        if (is_string($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors = array_merge($this->errors, $error);
        }

        return $this;
    }

    public function removeError($errorKey)
    {
        if (is_array($errorKey)) {
            foreach ($errorKey as $k) {
                $this->unsetError($k);
            }
        } else {
            $this->unsetError($errorKey);
        }

        return $this;
    }

    public function removeAllErrors()
    {
        $this->removeError(array_keys($this->errors));

        return $this;
    }

    public function unsetError($errorKey)
    {
        unset($this->errors[$errorKey]);
    }

    public function isError()
    {
        return !empty($this->errors);
    }

    public function data(array $data = [])
    {
        $this->responseData = array_merge($this->responseData, $data);

        return $this;
    }

    public function setStatusCode($code)
    {
        $this->statusCode = $code;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function isFails()
    {
        return $this->responseData['status'] == 'fail';
    }

    public function toArray()
    {
        $dataArray = $this->responseData;

        if (!empty($this->errors)) {
            $dataArray['status'] = 'fail';
            $dataArray['errors'] = $this->errors;
        }

        return $dataArray;
    }

    public function toJson($options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->toJson();
    }
}