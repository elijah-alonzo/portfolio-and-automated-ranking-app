<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluation Form Export</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }
        .header {
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 6px 0;
        }
        .meta {
            margin: 0;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }
        th {
            background: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Evaluation Form Export</h1>
        <p class="meta">Council: {{ $evaluation->council?->name ?? 'Council' }}</p>
        <p class="meta">Academic Year: {{ $evaluation->academic_year ?? 'Unknown Year' }}</p>
        <p class="meta">Evaluatee: {{ $evaluatee->name ?? 'Student' }}</p>
        <p class="meta">Evaluator: {{ $evaluator?->name ?? 'Unknown' }}</p>
        <p class="meta">Type: {{ ucfirst($evaluationType) }}</p>
        <p class="meta">Date Submitted: {{ $submittedAt?->format('Y-m-d H:i') ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 35%">Question</th>
                <th style="width: 35%">Options</th>
                <th style="width: 15%">Answer</th>
                <th style="width: 15%">Evaluatee</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['question'] }}</td>
                    <td>{{ $row['options'] }}</td>
                    <td>{{ $row['answer'] }}</td>
                    <td>{{ $row['evaluatee'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
