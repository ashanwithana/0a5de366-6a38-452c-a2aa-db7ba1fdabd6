<?php

namespace Tests\Feature;

use App\Services\DiagnosticReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiagnosticReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_generates_diagnostic_report_for_valid_student()
    {
        $report = app(DiagnosticReportService::class)->generate('student1');

        $this->assertStringContainsString('Tony Stark recently completed Numeracy assessment', $report);
        $this->assertStringContainsString('Number and Algebra:', $report);
    }

    public function test_it_handles_invalid_student()
    {
        $report = app(DiagnosticReportService::class)->generate('invalid-id');

        $this->assertEquals('Student not found.', $report);
    }
}
