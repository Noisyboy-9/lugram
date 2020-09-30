<?php

namespace AppTests;

use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /** @test * */
    public function sample_test()
    {
        $this->assertTrue(true);
    }
}
