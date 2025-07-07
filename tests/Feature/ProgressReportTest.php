<?php

namespace Tests\Feature;

use App\Services\ProgressReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProgressReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_it_generates_progress_report()
    {
        $report = app(ProgressReportService::class)->generate('student1');

        $this->assertStringContainsString('Tony Stark has completed Numeracy assessment', $report);
        $this->assertStringContainsString('Raw Score:', $report);
    }
}
