<?php

namespace Database\Seeders;

use App\Models\AwardType;
use App\Models\Council;
use App\Models\CouncilPosition;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        CouncilPosition::query()->delete();
        Position::query()->delete();
        User::query()->delete();
        Council::query()->delete();
        Department::query()->delete();
        AwardType::query()->delete();
        Schema::enableForeignKeyConstraints();

        $departments = [
            'University Administration' => 'Administrative and campus-wide oversight functions.',
            'School of Information Technology and Engineering' => 'Programs and initiatives under the School of Information Technology and Engineering.',
            'School of Art Sciences and Teacher Education' => 'Programs and initiatives under the School of Art Sciences and Teacher Education.',
            'School of Business, Accountancy, and Hospitality Management' => 'Programs and initiatives under the School of Business, Accountancy, and Hospitality Management.',
            'School of Nursing and Allied Health Services' => 'Programs and initiatives under the School of Nursing and Allied Health Services.',
        ];

        $departmentModels = [];
        foreach ($departments as $name => $description) {
            $departmentModels[$name] = Department::create([
                'name' => $name,
                'description' => $description,
            ]);
        }

        $awardTypes = [
            'Campus Leadership Award' => 'Recognizes an outstanding student leader within a specific school or college. This award honors individuals who have demonstrated exceptional service, initiative, and the ability to drive progress and engagement within their specialized academic department.',
            'Departmental Leadership Award' => 'A university-wide distinction awarded to a student leader who has demonstrated exemplary governance and impact across the entire campus. This honor recognizes those who have fostered institutional unity and led initiatives that benefit the entire Paulinian community.',
        ];

        $awardTypeModels = [];
        foreach ($awardTypes as $name => $description) {
            $awardTypeModels[$name] = AwardType::create([
                'name' => $name,
                'description' => $description,
            ]);
        }

        $campusLeadership = $awardTypeModels['Campus Leadership Award'];
        $departmentalLeadership = $awardTypeModels['Departmental Leadership Award'];

        $councils = [
            [
                'name' => 'Paulinian Student Government',
                'code' => 'PSG',
                'is_active' => true,
                'award_type_id' => $campusLeadership->id,
                'description' => 'The primary student governing body. Exercises campus-wide jurisdiction and oversees university-wide initiatives and student representation.',
            ],
            [
                'name' => 'Paulinian Student Government - SITE',
                'code' => 'PSG-SITE',
                'is_active' => true,
                'award_type_id' => $departmentalLeadership->id,
                'description' => 'The departmental student governing body for the School of Information Technology and Engineering.',
            ],
            [
                'name' => 'Paulinian Student Government - SASTE',
                'code' => 'PSG-SASTE',
                'is_active' => true,
                'award_type_id' => $departmentalLeadership->id,
                'description' => 'The departmental student governing body for the School of Art Sciences and Teacher Education.',
            ],
            [
                'name' => 'Paulinian Student Government - SBAHM',
                'code' => 'PSG-SBAHM',
                'is_active' => true,
                'award_type_id' => $departmentalLeadership->id,
                'description' => 'The departmental student governing body for the School of Business, Accountancy, and Hospitality Management.',
            ],
            [
                'name' => 'Paulinian Student Government - SNAHS',
                'code' => 'PSG-SNAHS',
                'is_active' => true,
                'award_type_id' => $departmentalLeadership->id,
                'description' => 'The departmental student governing body for the School of Nursing and Allied Health Services.',
            ],
        ];

        foreach ($councils as $council) {
            Council::create($council);
        }

        $councilModels = Council::all()->keyBy('code');
        $studentDepartments = collect([
            'School of Information Technology and Engineering',
            'School of Art Sciences and Teacher Education',
            'School of Business, Accountancy, and Hospitality Management',
            'School of Nursing and Allied Health Services',
        ])->map(fn ($name) => $departmentModels[$name]->id)->all();

        if (isset($councilModels['PSG'])) {
            $councilModels['PSG']->departments()->sync($studentDepartments);
        }

        $departmentCouncilMap = [
            'PSG-SITE' => 'School of Information Technology and Engineering',
            'PSG-SASTE' => 'School of Art Sciences and Teacher Education',
            'PSG-SBAHM' => 'School of Business, Accountancy, and Hospitality Management',
            'PSG-SNAHS' => 'School of Nursing and Allied Health Services',
        ];

        foreach ($departmentCouncilMap as $code => $departmentName) {
            if (!isset($councilModels[$code])) {
                continue;
            }

            $departmentId = $departmentModels[$departmentName]->id ?? null;
            if ($departmentId) {
                $councilModels[$code]->departments()->sync([$departmentId]);
            }
        }

        $defaultPassword = Hash::make('password');

        $users = [
            [
                'name' => 'Rucelj Pugeda',
                'email' => 'admin@psg.com',
                'contact_number' => '123456789',
                'role' => 'admin',
                'department_name' => 'University Administration',
                'bio' => 'A system user with administrative privileges. Responsible for managing core data, configuring system settings, and overseeing the overall integrity of the platform\'s workflows.',
            ],
            [
                'name' => 'Luigie Cagurangan',
                'email' => 'siteadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'department_name' => 'School of Information Technology and Engineering',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Reychelle Antonio',
                'email' => 'sasteadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'department_name' => 'School of Art Sciences and Teacher Education',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Serafin Gazzingan',
                'email' => 'snahsadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'department_name' => 'School of Nursing and Allied Health Services',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Justin Tan',
                'email' => 'sbahmsadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'department_name' => 'School of Business, Accountancy, and Hospitality Management',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Krisha Berbano',
                'email' => 'psgstudent1@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Information Technology and Engineering',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Yowelle Sedano',
                'email' => 'psgstudent2@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Art Sciences and Teacher Education',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Angel Perez',
                'email' => 'psgstudent3@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Business, Accountancy, and Hospitality Management',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Vinson Villanueva',
                'email' => 'psgstudent4@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Nursing and Allied Health Services',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Biegh Alonzo',
                'email' => 'sitestudent1@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Information Technology and Engineering',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Nathalee Bautista',
                'email' => 'sitestudent2@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Information Technology and Engineering',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Joshua CAbalza',
                'email' => 'sitestudent3@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Information Technology and Engineering',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Mikael Estillore',
                'email' => 'sitestudent4@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'department_name' => 'School of Information Technology and Engineering',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
        ];

        foreach ($users as $user) {
            $departmentId = $departmentModels[$user['department_name']]->id ?? null;
            User::create([
                ...collect($user)->except('department_name')->all(),
                'is_active' => true,
                'password' => $defaultPassword,
                'department_id' => $departmentId,
            ]);
        }

        $campusCouncils = Council::where('code', 'PSG')->get();
        $departmentalCouncils = Council::where('code', '!=', 'PSG')->get();

        $positions = [
            ['title' => 'President', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 1, 'availability' => 'campus'],
            ['title' => 'Vice President', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 2, 'availability' => 'campus'],
            ['title' => 'Governor', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 3, 'availability' => 'departmental'],
            ['title' => 'Vice Governor', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 4, 'availability' => 'departmental'],
            ['title' => 'Secretary', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 5, 'availability' => 'both'],
            ['title' => 'Assistant Secretary', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 6, 'availability' => 'both'],
            ['title' => 'Treasurer', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 7, 'availability' => 'both'],
            ['title' => 'Assistant Treasurer', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 8, 'availability' => 'both'],
            ['title' => 'Auditor', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 9, 'availability' => 'both'],
            ['title' => 'Public Relations Officer', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 10, 'availability' => 'both'],
            ['title' => 'Assistant Public Relations Officer', 'branch' => 'Executive', 'max_slots' => 1, 'hierarchy' => 11, 'availability' => 'both'],
            ['title' => 'Senate President', 'branch' => 'Legislative', 'max_slots' => 1, 'hierarchy' => 12, 'availability' => 'campus'],
            ['title' => 'Senate Secretary', 'branch' => 'Legislative', 'max_slots' => 1, 'hierarchy' => 13, 'availability' => 'campus'],
            ['title' => 'Senator', 'branch' => 'Legislative', 'max_slots' => 10, 'hierarchy' => 14, 'availability' => 'campus'],
            ['title' => 'Speaker of the House', 'branch' => 'Legislative', 'max_slots' => 1, 'hierarchy' => 15, 'availability' => 'campus'],
            ['title' => 'Secretary General', 'branch' => 'Legislative', 'max_slots' => 1, 'hierarchy' => 16, 'availability' => 'campus'],
            ['title' => 'Congress', 'branch' => 'Legislative', 'max_slots' => 6, 'hierarchy' => 17, 'availability' => 'campus'],
            ['title' => 'Councilors', 'branch' => 'Legislative', 'max_slots' => 8, 'hierarchy' => 18, 'availability' => 'departmental'],
            ['title' => 'Chief Justice', 'branch' => 'Judiciary', 'max_slots' => 1, 'hierarchy' => 19, 'availability' => 'campus'],
            ['title' => 'Justice Secretary', 'branch' => 'Judiciary', 'max_slots' => 1, 'hierarchy' => 20, 'availability' => 'campus'],
            ['title' => 'Associate Justices', 'branch' => 'Judiciary', 'max_slots' => 4, 'hierarchy' => 21, 'availability' => 'campus'],
            ['title' => 'Mayor', 'branch' => 'Mayoral', 'max_slots' => 4, 'hierarchy' => 22, 'availability' => 'departmental'],
        ];

        foreach ($positions as $positionData) {
            $position = Position::firstOrCreate(
                ['title' => $positionData['title']],
                [
                    'branch' => $positionData['branch'],
                    'hierarchy' => $positionData['hierarchy'],
                    'is_active' => true,
                ]
            );

            $targetCouncils = match ($positionData['availability']) {
                'departmental' => $departmentalCouncils,
                'both' => $campusCouncils->merge($departmentalCouncils),
                default => $campusCouncils,
            };

            foreach ($targetCouncils as $council) {
                CouncilPosition::updateOrCreate(
                    [
                        'council_id' => $council->id,
                        'position_id' => $position->id,
                    ],
                    [
                        'max_slots' => $positionData['max_slots'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
