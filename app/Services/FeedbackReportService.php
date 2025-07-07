<?php

namespace App\Services;

class FeedbackReportService
{
    public function generate(string $studentId): string
    {
        $students = json_decode(file_get_contents(base_path('data/students.json')), true);
        $responses = json_decode(file_get_contents(base_path('data/student-responses.json')), true);
        $assessments = json_decode(file_get_contents(base_path('data/assessments.json')), true);
        $questions = json_decode(file_get_contents(base_path('data/questions.json')), true);

        $student = collect($students)->firstWhere('id', $studentId);
        if (!$student) {
            return "Student not found.";
        }

        // Get latest completed assessment
        $latest = collect($responses)
            ->filter(fn($r) => $r['student']['id'] === $studentId && !empty($r['completed']))
            ->sortByDesc(fn($r) => \DateTime::createFromFormat('d/m/Y H:i:s', $r['completed']))
            ->first();

        if (!$latest) {
            return "No completed assessments found for this student.";
        }

        $fullName = "{$student['firstName']} {$student['lastName']}";
        $assessment = collect($assessments)->firstWhere('id', $latest['assessmentId']);
        $date = \DateTime::createFromFormat('d/m/Y H:i:s', $latest['completed'])->format('jS F Y h:i A');

        $total = count($latest['responses']);
        $correct = 0;

        $feedback = [];

        foreach ($latest['responses'] as $response) {
            $question = collect($questions)->firstWhere('id', $response['questionId']);
            if (!$question) continue;

            $correctOptionId = $question['config']['key'];
            $userOptionId = $response['response'];

            if ($userOptionId === $correctOptionId) {
                $correct++;
                continue;
            }

            $userOption = collect($question['config']['options'])->firstWhere('id', $userOptionId);
            $correctOption = collect($question['config']['options'])->firstWhere('id', $correctOptionId);

            $feedback[] = [
                'stem' => $question['stem'],
                'user_label' => $userOption['label'] ?? 'N/A',
                'user_value' => $userOption['value'] ?? 'N/A',
                'correct_label' => $correctOption['label'],
                'correct_value' => $correctOption['value'],
                'hint' => $question['config']['hint']
            ];
        }

        $report = "{$fullName} recently completed {$assessment['name']} assessment on {$date}\n";
        $report .= "He got {$correct} questions right out of {$total}. Feedback for wrong answers given below:\n\n";

        foreach ($feedback as $item) {
            $report .= "Question: {$item['stem']}\n";
            $report .= "Your answer: {$item['user_label']} with value {$item['user_value']}\n";
            $report .= "Right answer: {$item['correct_label']} with value {$item['correct_value']}\n";
            $report .= "Hint: {$item['hint']}\n\n";
        }

        return $report;
    }
}
