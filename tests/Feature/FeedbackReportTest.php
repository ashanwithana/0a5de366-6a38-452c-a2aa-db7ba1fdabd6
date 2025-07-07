<?php

namespace Tests\Feature;

use App\Services\FeedbackReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeedbackReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_it_generates_feedback_for_incorrect_answers()
    {
        $report = app(FeedbackReportService::class)->generate('student1');

        $this->assertStringContainsString('Feedback for wrong answers given below', $report);
        $this->assertStringContainsString('Hint:', $report);
    }
}
