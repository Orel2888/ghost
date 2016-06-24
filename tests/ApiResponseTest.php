<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Api\ApiResponse;

class ApiResponseTest extends TestCase
{
    protected $apiResponse;

    public function setUp()
    {
        $this->apiResponse = new ApiResponse();
    }

    public function test_ok()
    {
        $this->assertEquals([
            'status'    => 'ok'
        ], $this->apiResponse->ok()->toArray());
    }

    public function test_ok_with_data()
    {
        $response = $this->apiResponse->ok()->data(['mydata' => 'val'])->toArray();

        $this->assertEquals([
            'status'    => 'ok',
            'mydata'    => 'val'
        ], $response);
    }

    public function test_fail()
    {
        $this->assertEquals([
            'status'    => 'fail'
        ], $this->apiResponse->fail()->toArray());
    }

    public function test_fail_with_data()
    {
        $this->assertEquals([
            'status'    => 'fail',
            'message'   => 'error'
        ], $this->apiResponse->fail(['message' => 'error'])->toArray());
    }

    public function test_fail_with_errors()
    {
        $response = $this->apiResponse->ok();

        $response->error('Custom', 'Custom error');

        $this->assertEquals([
            'status'    => 'fail',
            'errors'    => ['Custom' => 'Custom error']
        ], $response->toArray());

        $response->error([
            'field1'    => 'filed1val',
            'field2'    => 'filed2val'
        ]);

        $this->assertTrue($response->isError());

        $this->assertEquals([
            'status'    => 'fail',
            'errors'    => [
                'Custom' => 'Custom error',
                'field1'    => 'filed1val',
                'field2'    => 'filed2val'
            ]
        ], $response->toArray());

        $response->removeError('Custom');

        $this->assertEquals([
            'status'    => 'fail',
            'errors'    => [
                'field1'    => 'filed1val',
                'field2'    => 'filed2val'
            ]
        ], $response->toArray());

        $response->removeError([
            'field1',
            'field2'
        ]);

        $this->assertEquals([
            'status'    => 'ok'
        ], $response->toArray());

        $response->error([
            'field1'    => 'filed1val',
            'field2'    => 'filed2val'
        ]);

        $response->removeAllErrors();

        $this->assertEquals([
            'status'    => 'ok'
        ], $response->toArray());
    }

    public function test_status_code()
    {
        $response = $this->apiResponse->ok()->setStatusCode(401);

        $this->assertEquals(401, $response->getStatusCode());
    }

}
