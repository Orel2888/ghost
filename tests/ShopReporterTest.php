<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Shop\ShopReporter;

class ShopReporterTest extends TestCase
{
    use BaseTestsHelper;

    /**
     * @var ShopReporter
     */
    protected $shopReporter;

    protected static $endTest = false;

    public function setUp()
    {
        parent::setUp();

        $this->shopReporter = new ShopReporter();

        if (!count(self::$database)) {
            $this->createData();
        }
    }

    public function test_get_price_list()
    {
        $priceList = $this->shopReporter->getPriceList();

        var_dump($priceList);

        self::$endTest = true;
    }

    public function tearDown()
    {
        if (self::$endTest) {
            self::removeData();
        }

        parent::tearDown();
    }
}
