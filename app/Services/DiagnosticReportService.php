<?php

namespace App\Services;

class DiagnosticReportService
{
    public function generate(string $studentId): string
    {
        $students = json_decode(file_get_contents(base_path('data/students.json')), true);
        $assessments = json_decode(file_get_contents(base_path('data/assessments.json')), true);
        $questions = json_decode(file_get_contents(base_path('data/questions.json')), true);
        $responses = json_decode(file_get_contents(base_path('data/student-responses.json')), true);

        $student = collect($students)->firstWhere('id', $studentId);
        if (!$student) {
            return "Student not found.";
        }

        // get latest completed assessment
        $studentAssessments = collect($responses)
            ->filter(fn($r) => $r['student']['id'] === $studentId && !empty($r['completed']))
            ->sortByDesc(fn($r) => \DateTime::createFromFormat('d/m/Y H:i:s', $r['completed']));

        if ($studentAssessments->isEmpty()) {
            return "No completed assessments found for this student.";
        }

        $latest = $studentAssessments->first();
        $assessment = collect($assessments)->firstWhere('id', $latest['assessmentId']);

        // ciorrect answer 
        $correctByStrand = [];
        $totalByStrand = [];

        foreach ($latest['responses'] as $response) {
            $question = collect($questions)->firstWhere('id', $response['questionId']);
            if (!$question) continue;

            $strand = $question['strand'];
            $correctOption = $question['config']['key'];
            $studentAnswer = $response['response'];

            $totalByStrand[$strand] = ($totalByStrand[$strand] ?? 0) + 1;

            if ($studentAnswer === $correctOption) {
                $correctByStrand[$strand] = ($correctByStrand[$strand] ?? 0) + 1;
            }
        }

        $correctTotal = array_sum($correctByStrand);
        $totalQuestions = array_sum($totalByStrand);

        $fullName = "{$student['firstName']} {$student['lastName']}";
        $date = \DateTime::createFromFormat('d/m/Y H:i:s', $latest['completed'])->format('jS F Y h:i A');

        $report = "{$fullName} recently completed {$assessment['name']} assessment on {$date}" . PHP_EOL;
        $report .= "He got {$correctTotal} questions right out of {$totalQuestions}. Details by strand given below:" . PHP_EOL . PHP_EOL;

        foreach ($totalByStrand as $strand => $count) {
            $correct = $correctByStrand[$strand] ?? 0;
            $report .= "{$strand}: {$correct} out of {$count} correct" . PHP_EOL;
        }

        return $report;
    }
}
