<?php

namespace Database\Seeders;

use App\Features\Awards\Models\AwardType;
use App\Features\Councils\Models\Council;
use App\Features\Councils\Models\CouncilPosition;
use App\Features\Departments\Models\Department;
use App\Features\Positions\Models\Position;
use App\Features\Users\Models\User;
use Illuminate\Database\Seeder;

/**
 * What a fresh deployment needs: PSG's departments, award types, councils and positions, and one admin.
 * Safe to run again: it fills in what is missing and never deletes.
 *
 * Sample people and evaluations live in DemoDataSeeder and only run when asked for.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = $this->departments();
        $awardTypes = $this->awardTypes();
        $this->councils($departments, $awardTypes);
        $this->positions();
        $this->admin($departments['University Administration']);
    }

    /**
     * @return array<string, Department>
     */
    private function departments(): array
    {
        $departments = [
            'University Administration' => 'Administrative and campus-wide oversight functions.',
            'School of Information Technology and Engineering' => 'Programs and initiatives under the School of Information Technology and Engineering.',
            'School of Art Sciences and Teacher Education' => 'Programs and initiatives under the School of Art Sciences and Teacher Education.',
            'School of Business, Accountancy, and Hospitality Management' => 'Programs and initiatives under the School of Business, Accountancy, and Hospitality Management.',
            'School of Nursing and Allied Health Services' => 'Programs and initiatives under the School of Nursing and Allied Health Services.',
        ];

        return collect($departments)
            ->map(fn (string $description, string $name) => Department::firstOrCreate(['name' => $name], ['description' => $description]))
            ->all();
    }

    /**
     * @return array<string, AwardType>
     */
    private function awardTypes(): array
    {
        $awardTypes = [
            'Campus Leadership Award' => 'A university-wide distinction for a student leader who has shown exemplary governance and impact across the entire campus, fostering institutional unity and leading initiatives that benefit the whole Paulinian community.',
            'Departmental Leadership Award' => 'Recognizes an outstanding student leader within a specific school, honoring exceptional service, initiative, and the ability to drive progress and engagement in their department.',
        ];

        return collect($awardTypes)
            ->map(fn (string $description, string $name) => AwardType::firstOrCreate(['name' => $name], ['description' => $description]))
            ->all();
    }

    /**
     * The campus-wide PSG and one departmental PSG per school, each linked to the departments its officers come from.
     *
     * @param  array<string, Department>  $departments
     * @param  array<string, AwardType>  $awardTypes
     */
    private function councils(array $departments, array $awardTypes): void
    {
        $schools = [
            'SITE' => 'School of Information Technology and Engineering',
            'SASTE' => 'School of Art Sciences and Teacher Education',
            'SBAHM' => 'School of Business, Accountancy, and Hospitality Management',
            'SNAHS' => 'School of Nursing and Allied Health Services',
        ];

        $campus = Council::firstOrCreate(['code' => 'PSG'], [
            'name' => 'Paulinian Student Government',
            'is_active' => true,
            'award_type_id' => $awardTypes['Campus Leadership Award']->id,
            'description' => 'The primary student governing body. Exercises campus-wide jurisdiction and oversees university-wide initiatives and student representation.',
        ]);
        $campus->departments()->syncWithoutDetaching(collect($schools)->map(fn (string $school) => $departments[$school]->id)->values()->all());

        foreach ($schools as $code => $school) {
            $council = Council::firstOrCreate(['code' => "PSG-{$code}"], [
                'name' => "Paulinian Student Government - {$code}",
                'is_active' => true,
                'award_type_id' => $awardTypes['Departmental Leadership Award']->id,
                'description' => "The departmental student governing body for the {$school}.",
            ]);
            $council->departments()->syncWithoutDetaching([$departments[$school]->id]);
        }
    }

    /**
     * Every PSG position, in order of rank, attached to the councils that have it.
     */
    private function positions(): void
    {
        $campus = Council::where('code', 'PSG')->get();
        $departmental = Council::where('code', '!=', 'PSG')->get();

        // [title, branch, slots per council, which councils have it]
        $positions = [
            ['President', 'Executive', 1, 'campus'],
            ['Vice President', 'Executive', 1, 'campus'],
            ['Governor', 'Executive', 1, 'departmental'],
            ['Vice Governor', 'Executive', 1, 'departmental'],
            ['Secretary', 'Executive', 1, 'both'],
            ['Assistant Secretary', 'Executive', 1, 'both'],
            ['Treasurer', 'Executive', 1, 'both'],
            ['Assistant Treasurer', 'Executive', 1, 'both'],
            ['Auditor', 'Executive', 1, 'both'],
            ['Public Relations Officer', 'Executive', 1, 'both'],
            ['Assistant Public Relations Officer', 'Executive', 1, 'both'],
            ['Senate President', 'Legislative', 1, 'campus'],
            ['Senate Secretary', 'Legislative', 1, 'campus'],
            ['Senator', 'Legislative', 10, 'campus'],
            ['Speaker of the House', 'Legislative', 1, 'campus'],
            ['Secretary General', 'Legislative', 1, 'campus'],
            ['Congress', 'Legislative', 6, 'campus'],
            ['Councilors', 'Legislative', 8, 'departmental'],
            ['Chief Justice', 'Judiciary', 1, 'campus'],
            ['Justice Secretary', 'Judiciary', 1, 'campus'],
            ['Associate Justices', 'Judiciary', 4, 'campus'],
            ['Mayor', 'Mayoral', 4, 'departmental'],
        ];

        foreach ($positions as $index => [$title, $branch, $maxSlots, $availability]) {
            $position = Position::firstOrCreate(['title' => $title], [
                'branch' => $branch,
                'hierarchy' => $index + 1,
                'is_active' => true,
            ]);

            $councils = match ($availability) {
                'campus' => $campus,
                'departmental' => $departmental,
                default => $campus->merge($departmental),
            };

            foreach ($councils as $council) {
                CouncilPosition::firstOrCreate(
                    ['council_id' => $council->id, 'position_id' => $position->id],
                    ['max_slots' => $maxSlots, 'is_active' => true],
                );
            }
        }
    }

    /**
     * The first admin, from config/app.php (ADMIN_EMAIL, ADMIN_PASSWORD). Set them before seeding a real deployment.
     */
    private function admin(Department $department): void
    {
        User::firstOrCreate(['email' => config('app.admin.email')], [
            'name' => config('app.admin.name'),
            'password' => config('app.admin.password'),
            'role' => 'admin',
            'department_id' => $department->id,
            'is_active' => true,
        ]);
    }
}
