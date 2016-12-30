<?php

class BaseApiException extends Exception
{
    private $rawContent;

    private $statusCode;

    public function setResponseContent($rawContent)
    {
        return $this->rawContent = $rawContent;
    }

    public function getResponseJson()
    {
        return json_decode($this->rawContent);
    }

    public function hasApiResponse()
    {
        return !is_null($this->rawContent);
    }

    public function setStatusCode($code)
    {
        return $this->statusCode = $code;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}