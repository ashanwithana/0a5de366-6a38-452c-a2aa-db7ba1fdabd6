<?php

namespace App\Console\Commands;

use App\Services\DiagnosticReportService;
use App\Services\FeedbackReportService;
use App\Services\ProgressReportService;
use Illuminate\Console\Command;

class GenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate a report for a student';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Prompt the user for the student ID and report type
        $studentId = $this->ask('Please enter the Student ID');
        $reportChoice = $this->ask('Report to generate (1 for Diagnostic, 2 for Progress, 3 for Feedback)');

        $this->line("");

        switch ($reportChoice) {
            case '1':
                $report = new DiagnosticReportService();
                break;
            case '2':
                $report = new ProgressReportService();
                break;
            case '3':
                $report = new FeedbackReportService();
                break;
            default:
                $this->error("Invalid report type. Choose 1, 2, or 3.");
                return 1;
        }

        $output = $report->generate($studentId);

        $this->line($output);
        return 0;
    }
}
