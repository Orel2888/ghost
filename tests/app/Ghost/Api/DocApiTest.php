<?php

class DocApiTest extends TestCase
{

    /**
     * @var DocApi
     */
    public $docApi;

    public function setUp()
    {
        parent::setUp();

        $this->docApi = new DocApi('api_docs_test.json');
        $this->docApi->clearDocs();
    }

    public function test_write_docs_to_json()
    {
        $test_requests = [
            'users.reg',
            'POST',
            ['l' => '1', 'p' => 2],
            ['data' => []]
        ];

        $addMethod = $this->docApi->writeDocApi(...$test_requests);

        $this->assertTrue($addMethod);

        $addMethod = $this->docApi->writeDocApi(...$test_requests);

        $this->assertFalse($addMethod);

        $test_requests[1] = 'GET';

        $addMethod = $this->docApi->writeDocApi(...$test_requests);

        $this->assertTrue($addMethod);

        $test_requests[0] = 'users.login';

        $addMethod = $this->docApi->writeDocApi(...$test_requests);

        $this->assertTrue($addMethod);
    }
}