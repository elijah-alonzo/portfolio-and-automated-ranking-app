<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Contracts\Support\Htmlable;

class TableWidget extends BaseTableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string|Htmlable|null
    {
        $user = auth()->user();

        if ($user && $user->role === 'admin') {
            return 'Pending Evaluations';
        }

        return 'Pending Evaluations';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        $columns = [
            TextColumn::make('council.name')
                ->label('Council')
                ->searchable(),
        ];

        if ($isAdmin) {
            $columns[] = TextColumn::make('adviser.name')
                ->label('Adviser')
                ->searchable();
        } else {
            $columns[] = ImageColumn::make('students_images')
                ->label('Students')
                ->stacked()
                ->limit(4)
                ->limitedRemainingText()
                ->circular()
                ->getStateUsing(function ($record) {
                    return $record->users->map(function ($student) {
                        return self::resolveProfileImageUrl($student->pfp, $student->name);
                    })->toArray();
                })
                ->tooltip(function ($record) {
                    $studentNames = $record->users->pluck('name')->toArray();

                    if (empty($studentNames)) {
                        return 'No students assigned';
                    }

                    return 'Students: '.implode(', ', $studentNames);
                });
        }

        $columns[] = TextColumn::make('academic_year')
            ->label('Academic Year')
            ->sortable();

        return $table
            ->query(function () use ($user) {
                $query = Evaluation::query()
                    ->with(['council', 'adviser', 'users'])
                    ->whereIn('status', ['closed', 'ongoing']);

                if (! $user) {
                    return $query->whereRaw('1 = 0');
                }

                if ($user->role === 'admin') {
                    return $query;
                }

                return $query->where(function ($evaluationQuery) use ($user) {
                    $evaluationQuery->where('council_adviser_id', $user->id)
                        ->orWhereHas('users', function ($subQuery) use ($user) {
                            $subQuery->where('user_id', $user->id);
                        });
                });
            })
            ->columns($columns)
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    protected static function resolveProfileImageUrl(?string $pfp, string $name): string
    {
        $fallbackUrl = 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';

        if (blank($pfp)) {
            return $fallbackUrl;
        }

        $path = str_replace('\\', '/', $pfp);

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $relativePath = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : ltrim($path, '/');

        return asset('storage/'.$relativePath);
    }
}
