<?php

namespace App\Services;

class ProgressReportService
{
    public function generate(string $studentId): string
    {
        $students = json_decode(file_get_contents(base_path('data/students.json')), true);
        $responses = json_decode(file_get_contents(base_path('data/student-responses.json')), true);
        $assessments = json_decode(file_get_contents(base_path('data/assessments.json')), true);

        $student = collect($students)->firstWhere('id', $studentId);
        if (!$student) {
            return "Student not found.";
        }

        // get latest completed assessment
        $studentAssessments = collect($responses)
            ->filter(fn($r) => $r['student']['id'] === $studentId && !empty($r['completed']))
            ->sortBy(fn($r) => \DateTime::createFromFormat('d/m/Y H:i:s', $r['completed']));

        if ($studentAssessments->isEmpty()) {
            return "No completed assessments found for this student.";
        }

        $fullName = "{$student['firstName']} {$student['lastName']}";
        $assessmentName = collect($assessments)->firstWhere('id', $studentAssessments->first()['assessmentId'])['name'];

        $report = "{$fullName} has completed {$assessmentName} assessment {$studentAssessments->count()} times in total. Date and raw score given below:\n";

        $scores = [];

        foreach ($studentAssessments as $r) {
            $date = \DateTime::createFromFormat('d/m/Y H:i:s', $r['assigned'])->format('jS F Y');
            $raw = $r['results']['rawScore'] ?? 'N/A';
            $report .= "\nDate: {$date}, Raw Score: {$raw} out of 16";
            $scores[] = $raw;
        }

        // Improvement calculation
        if (count($scores) > 1) {
            $improvement = $scores[count($scores) - 1] - $scores[0];
            $report .= "\n\n{$fullName} got {$improvement} more correct in the recent completed assessment than the oldest";
        }

        return $report;
    }
}
