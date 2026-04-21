<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Roll back each test in a transaction instead of migrate:fresh (RefreshDatabase),
     * so running the suite never drops or wipes your PostgreSQL data.
     */
    use DatabaseTransactions;
}
