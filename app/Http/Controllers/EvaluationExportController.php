<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvaluationExportController extends Controller
{
    public function export(Request $request, Evaluation $evaluation, User $user, string $type, string $format)
    {
        $authUser = $request->user();

        $isAdmin = $authUser && $authUser->role === 'admin';
        $isAssignedAdviser = $authUser
            && $authUser->role === 'adviser'
            && $evaluation->council_adviser_id === $authUser->id;

        if (! $isAdmin && ! $isAssignedAdviser) {
            abort(403, 'You are not authorized to export this evaluation.');
        }

        if (! in_array($type, ['self', 'peer', 'adviser'], true)) {
            abort(404, 'Invalid evaluation type');
        }

        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(404, 'Invalid export format');
        }

        $evaluation->loadMissing('council');

        $form = EvaluationForm::query()
            ->where('evaluation_id', $evaluation->id)
            ->where('user_id', $user->id)
            ->where('evaluator_type', $type)
            ->with('evaluator')
            ->firstOrFail();

        $questions = EvaluationForm::getQuestionsForEvaluator($type);
        $rows = $this->buildRows($questions, $form, $user);

        if ($format === 'csv') {
            return $this->downloadCsv($rows, $evaluation, $user, $type);
        }

        return $this->downloadPdf($rows, $evaluation, $user, $type, $form);
    }

    private function buildRows(array $questions, EvaluationForm $form, User $evaluatee): array
    {
        $rows = [];
        foreach ($questions as $key => $question) {
            $options = $this->formatOptions($question['criteria'] ?? []);
            $rows[] = [
                'question' => $question['text'] ?? $key,
                'options' => $options,
                'answer' => $form->answers[$key] ?? '',
                'evaluatee' => $evaluatee->name ?? 'Unknown',
                'evaluator' => $form->evaluator?->name ?? 'Unknown',
            ];
        }

        return $rows;
    }

    private function formatOptions(array $criteria): string
    {
        $parts = [];
        foreach ($criteria as $score => $text) {
            $parts[] = $score.' - '.$text;
        }

        return implode(' | ', $parts);
    }

    private function downloadCsv(array $rows, Evaluation $evaluation, User $evaluatee, string $type): StreamedResponse
    {
        $filename = $this->buildFilename($evaluation, $evaluatee, $type, 'csv');

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Question', 'Options', 'Answer Selected', 'Evaluatee', 'Evaluator']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['question'],
                    $row['options'],
                    $row['answer'],
                    $row['evaluatee'],
                    $row['evaluator'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function downloadPdf(array $rows, Evaluation $evaluation, User $evaluatee, string $type, EvaluationForm $form)
    {
        $filename = $this->buildFilename($evaluation, $evaluatee, $type, 'pdf');

        $pdf = Pdf::loadView('EvaluationForm.EvaluationExportPdf', [
            'evaluation' => $evaluation,
            'evaluatee' => $evaluatee,
            'evaluator' => $form->evaluator,
            'evaluationType' => $type,
            'submittedAt' => $form->updated_at,
            'rows' => $rows,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    private function buildFilename(Evaluation $evaluation, User $evaluatee, string $type, string $extension): string
    {
        $council = $evaluation->council?->name ?? 'council';
        $council = preg_replace('/[^A-Za-z0-9_-]+/', '_', $council);
        $name = $evaluatee->name ?? 'student';
        $name = preg_replace('/[^A-Za-z0-9_-]+/', '_', $name);

        return strtolower("evaluation_{$council}_{$name}_{$type}.{$extension}");
    }
}
