<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * phpunit.xml points tests at SQLite :memory:. Migrations run once per process;
     * each test is wrapped so data does not leak between tests.
     */
    use RefreshDatabase;
}
