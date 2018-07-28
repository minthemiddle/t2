<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class TaskAirCommandTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testInspiringCommand(): void
    {
        Artisan::call('task:air');

        $this->assertContains('Not enough arguments (missing: "name")', Artisan::output());
    }
}
