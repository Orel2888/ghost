<?php

class DocApi
{
    public $pathDocFile;

    public function __construct($docFile = null)
    {
        if (is_null($docFile)) {
            $this->pathDocFile = 'api_docs.json';
        } else {
            $this->pathDocFile = $docFile;
        }
    }

    public function writeDocApi($apiMethod, $httpMethod, $formParams, $response)
    {
        $docs = $this->getDocApi(true);

        $chunksMethod = explode('.', $apiMethod);

        list($firstNameMethod, $lastNameMethod) = $chunksMethod;

        if (!isset($docs[$firstNameMethod])) {
            $docs[$firstNameMethod] = [];
        }

        if (!isset($docs[$firstNameMethod]['requests'])) {
            $docs[$firstNameMethod]['requests'] = [];
        }

        $docRequests = $docs[$firstNameMethod]['requests'];

        $duplicateRequests = array_filter($docRequests, function ($docRequest) use($apiMethod, $httpMethod, $formParams, $response) {
            return $docRequest['method'] == $apiMethod
                && $docRequest['http_method'] == $httpMethod
                && $docRequest['form_params'] == $formParams
                && $docRequest['response'] == $response;
        });

        $docUpdated = false;

        if (!count($duplicateRequests)) {
            $docRequests[] = [
                'method'        => $apiMethod,
                'http_method'   => $httpMethod,
                'form_params'   => $formParams,
                'response'      => $response
            ];

            $docs[$firstNameMethod]['requests'] = $docRequests;
            $docUpdated = true;
        }

        if ($docUpdated) {
            Storage::put($this->pathDocFile, json_encode($docs, JSON_PRETTY_PRINT));
        }

        return $docUpdated;
    }

    public function getDocApi($toArray = false)
    {
        return Storage::has($this->pathDocFile) ? json_decode(Storage::get($this->pathDocFile), $toArray) : [];
    }

    public function clearDocs()
    {
        Storage::put($this->pathDocFile, json_encode([]));

        return $this;
    }
}