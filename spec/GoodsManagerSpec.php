<?php

namespace spec\App;

use App\GoodsManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GoodsManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GoodsManager::class);
    }
}
