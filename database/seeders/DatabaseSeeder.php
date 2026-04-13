<?php

namespace Database\Seeders;

use App\Models\AwardType;
use App\Models\Council;
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
        User::query()->delete();
        Council::query()->delete();
        AwardType::query()->delete();
        Schema::enableForeignKeyConstraints();

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

        $defaultPassword = Hash::make('password');

        $users = [
            [
                'name' => 'Rucelj Pugeda',
                'email' => 'admin@psg.com',
                'contact_number' => '123456789',
                'role' => 'admin',
                'bio' => 'A system user with administrative privileges. Responsible for managing core data, configuring system settings, and overseeing the overall integrity of the platform\'s workflows.',
            ],
            [
                'name' => 'Luigie Cagurangan',
                'email' => 'siteadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Reychelle Antonio',
                'email' => 'sasteadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Serafin Gazzingan',
                'email' => 'snahsadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Justin Tan',
                'email' => 'sbahmsadviser@psg.com',
                'contact_number' => '123456789',
                'role' => 'adviser',
                'bio' => 'A system user with supervisory privileges. Responsible for reviewing submissions, verifying departmental records, and providing guidance within their assigned jurisdiction.',
            ],
            [
                'name' => 'Krisha Berbano',
                'email' => 'psgstudent1@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Yowelle Sedano',
                'email' => 'psgstudent2@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Angel Perez',
                'email' => 'psgstudent3@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Vinson Villanueva',
                'email' => 'psgstudent4@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Biegh Alonzo',
                'email' => 'sitestudent1@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Nathalee Bautista',
                'email' => 'sitestudent2@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Joshua CAbalza',
                'email' => 'sitestudent3@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
            [
                'name' => 'Mikael Estillore',
                'email' => 'sitestudent4@psg.com',
                'contact_number' => '123456789',
                'role' => 'student',
                'bio' => 'A system user with standard privileges. Responsible for managing personal submissions, tracking leadership involvement, and maintaining an updated record of their academic and extracurricular activities.',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                ...$user,
                'is_active' => true,
                'password' => $defaultPassword,
            ]);
        }
    }
}
