<?php

namespace Database\Seeders;

use App\Features\Awards\Models\AwardType;
use App\Features\Awards\Models\LeadershipAwardApplication;
use App\Features\Certificates\Models\Certificate;
use App\Features\Councils\Models\Council;
use App\Features\Departments\Models\Department;
use App\Features\Evaluations\Enums\EvaluatorType;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Evaluations\Models\EvaluationPositionSlot;
use App\Features\Users\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\DatabaseNotification as FilamentDatabaseNotification;
use Filament\Notifications\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Realistic sample data for screenshots: two completed PSG-SITE years with ranks, an open year with forms
 * in every state, a campus council still being set up, certificates, an award application and notifications.
 * Every evaluation moves through the same models and score hooks the app uses.
 *
 * Not part of a normal seed. Run it on an empty database: php artisan migrate:fresh --seeder=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    /** @var array<string, User> */
    private array $people = [];

    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('certificates/demo');
        DB::table('notifications')->delete();

        $this->call(DatabaseSeeder::class);
        $this->people();

        $adviser = $this->person('siteadviser@psg.com');
        $site = Council::where('code', 'PSG-SITE')->firstOrFail();
        $campus = Council::where('code', 'PSG')->firstOrFail();

        // Each officer's typical rating, so scores and ranks vary the way they would in real life.
        $level = [
            'sitestudent4@psg.com' => 2.8, // Elijah
            'psgstudent1@psg.com' => 2.6,  // Krisha
            'sitestudent1@psg.com' => 2.2, // Biegh
            'sitestudent2@psg.com' => 1.9, // Nathalee
            'sitestudent3@psg.com' => 1.5, // Joshua
        ];

        $this->completedYear($site, $adviser, '2024-2025', [
            'Governor' => 'psgstudent1@psg.com',
            'Vice Governor' => 'sitestudent4@psg.com',
            'Secretary' => 'sitestudent1@psg.com',
            'Treasurer' => 'sitestudent2@psg.com',
            'Auditor' => 'sitestudent3@psg.com',
        ], $level);

        $this->completedYear($site, $adviser, '2025-2026', [
            'Governor' => 'sitestudent4@psg.com',
            'Vice Governor' => 'psgstudent1@psg.com',
            'Secretary' => 'sitestudent2@psg.com',
            'Treasurer' => 'sitestudent1@psg.com',
            'Auditor' => 'sitestudent3@psg.com',
        ], $level);

        $this->openYear($site, $adviser, $level);
        $this->campusSetup($campus, $adviser);
        $this->certificates($adviser);
        $this->awardApplication();
        $this->notifications($adviser);
    }

    /**
     * @param  array<string, string>  $seats  position title => officer email
     * @param  array<string, float>  $level
     */
    private function completedYear(Council $council, User $adviser, string $year, array $seats, array $level): void
    {
        $evaluation = $this->evaluation($council, $adviser, $year, $seats);

        foreach ($seats as $email) {
            foreach (EvaluatorType::cases() as $type) {
                $this->form($evaluation, $this->person($email), $type, $level[$email], 'submitted');
            }
        }

        $evaluation->update(['status' => Evaluation::STATUS_COMPLETED]);
        $this->backdate($evaluation, Carbon::create((int) substr($year, 5, 4), 3, 20));
    }

    /**
     * The current year, open for forms: some officers are done, some are halfway, some haven't started.
     *
     * @param  array<string, float>  $level
     */
    private function openYear(Council $council, User $adviser, array $level): void
    {
        $seats = [
            'Governor' => 'sitestudent4@psg.com',
            'Vice Governor' => 'psgstudent1@psg.com',
            'Secretary' => 'sitestudent1@psg.com',
            'Treasurer' => 'sitestudent2@psg.com',
        ];

        $evaluation = $this->evaluation($council, $adviser, '2026-2027', $seats);

        // [self, peer, adviser] for each officer.
        $progress = [
            'psgstudent1@psg.com' => ['submitted', 'submitted', 'submitted'],
            'sitestudent1@psg.com' => ['submitted', 'draft', 'draft'],
            'sitestudent2@psg.com' => ['draft', 'submitted', null],
            'sitestudent4@psg.com' => [null, 'draft', null],
        ];

        foreach ($progress as $email => [$self, $peer, $adviserForm]) {
            $officer = $this->person($email);

            foreach ([[EvaluatorType::Self, $self], [EvaluatorType::Peer, $peer], [EvaluatorType::Adviser, $adviserForm]] as [$type, $status]) {
                if ($status) {
                    $this->form($evaluation, $officer, $type, $level[$email], $status);
                }
            }
        }
    }

    /**
     * The campus council for this year, still closed while the adviser fills its positions.
     */
    private function campusSetup(Council $council, User $adviser): void
    {
        $this->evaluation($council, $adviser, '2026-2027', [
            'President' => 'psgstudent1@psg.com',
            'Vice President' => 'psgstudent2@psg.com',
            'Secretary' => 'psgstudent3@psg.com',
            'Treasurer' => 'psgstudent4@psg.com',
        ], status: Evaluation::STATUS_CLOSED, pairEveryone: false);
    }

    /**
     * Creates the evaluation (which creates its position slots), seats the officers and pairs peers in a ring.
     *
     * @param  array<string, string>  $seats
     */
    private function evaluation(Council $council, User $adviser, string $year, array $seats, string $status = Evaluation::STATUS_ONGOING, bool $pairEveryone = true): Evaluation
    {
        $evaluation = Evaluation::create([
            'council_id' => $council->id,
            'council_adviser_id' => $adviser->id,
            'academic_year' => $year,
            'status' => $status,
        ]);

        $officers = [];

        foreach ($seats as $title => $email) {
            $officer = $this->person($email);
            $slot = EvaluationPositionSlot::where('evaluation_id', $evaluation->id)
                ->whereNull('user_id')
                ->whereHas('position', fn ($query) => $query->where('title', $title))
                ->orderBy('slot_number')
                ->firstOrFail();

            $slot->update(['user_id' => $officer->id]);
            $evaluation->users()->attach($officer->id, ['position' => $title]);
            $officers[] = $officer;
        }

        // While setting up, the adviser has paired only the first two officers so far.
        $pairCount = $pairEveryone ? count($officers) : min(2, count($officers));

        foreach (array_slice($officers, 0, $pairCount) as $index => $officer) {
            EvaluationPeerEvaluator::create([
                'evaluation_id' => $evaluation->id,
                'evaluatee_user_id' => $officer->id,
                'evaluator_user_id' => $officers[($index + 1) % count($officers)]->id,
                'assigned_by_user_id' => $adviser->id,
                'assigned_at' => now(),
            ]);
        }

        return $evaluation;
    }

    /**
     * One form, rated around the officer's typical level. Saving it runs the app's score and rank hooks.
     */
    private function form(Evaluation $evaluation, User $officer, EvaluatorType $type, float $level, string $status): void
    {
        $questions = array_keys(EvaluationForm::getQuestionsForEvaluator($type->value));
        // Officers rate themselves a little higher, peers a little lower, as they tend to.
        $bias = match ($type) {
            EvaluatorType::Self => 0.2,
            EvaluatorType::Peer => -0.1,
            EvaluatorType::Adviser => 0.0,
        };

        $answers = [];

        foreach ($questions as $index => $key) {
            // A fixed wobble per question keeps the data the same on every run.
            $wobble = [0.3, -0.4, 0.1, -0.2, 0.4, -0.1, 0.2, -0.3][($index + $officer->id) % 8];
            $answers[$key] = max(0, min(3, (int) round($level + $bias + $wobble)));
        }

        if ($type === EvaluatorType::Adviser) {
            $answers[EvaluationForm::LENGTH_OF_SERVICE_KEY] = EvaluationForm::getLengthOfServiceData($evaluation, $officer->id)['score'];
        }

        // A draft is only part done.
        if ($status === 'draft') {
            $answers = array_slice($answers, 0, (int) ceil(count($answers) / 2), true);
        }

        $evaluator = match ($type) {
            EvaluatorType::Self => $officer,
            EvaluatorType::Adviser => $evaluation->adviser,
            EvaluatorType::Peer => EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
                ->where('evaluatee_user_id', $officer->id)
                ->firstOrFail()
                ->evaluatorUser,
        };

        EvaluationForm::create([
            'evaluation_id' => $evaluation->id,
            'user_id' => $officer->id,
            'evaluator_type' => $type->value,
            'evaluator_id' => $evaluator->id,
            'answers' => $answers,
            'status' => $status,
        ]);
    }

    private function certificates(User $adviser): void
    {
        $issued = [
            ['Leadership Training Seminar 2025', '2025-02-14', ['sitestudent4@psg.com', 'psgstudent1@psg.com', 'sitestudent1@psg.com', 'sitestudent2@psg.com', 'sitestudent3@psg.com']],
            ['Outstanding Governor, PSG-SITE 2025-2026', '2026-04-10', ['sitestudent4@psg.com']],
            ['Paulinian Leaders\' Camp 2025 Facilitator', '2025-10-18', ['sitestudent4@psg.com', 'psgstudent1@psg.com']],
            ['Certificate of Service, PSG-SITE 2024-2025', '2025-04-05', ['psgstudent1@psg.com', 'sitestudent4@psg.com', 'sitestudent1@psg.com', 'sitestudent2@psg.com', 'sitestudent3@psg.com']],
        ];

        foreach ($issued as $index => [$name, $date, $emails]) {
            $path = "certificates/demo/certificate-{$index}.pdf";
            Storage::disk('public')->put($path, $this->certificatePdf($name, $date, $adviser));

            foreach ($emails as $email) {
                Certificate::create([
                    'user_id' => $this->person($email)->id,
                    'file_path' => $path,
                    'certification_name' => $name,
                    'date_issued' => $date,
                ]);
            }
        }
    }

    private function certificatePdf(string $name, string $date, User $adviser): string
    {
        $issuedOn = Carbon::parse($date)->format('F j, Y');

        return Pdf::loadHTML(<<<HTML
            <html><body style="font-family: Times, serif; text-align: center; padding: 60px 40px; border: 10px double #036635; color: #01331d;">
                <p style="letter-spacing: 3px; font-size: 13px;">PAULINIAN STUDENT GOVERNMENT</p>
                <h1 style="font-size: 34px; margin: 30px 0 10px;">Certificate of Recognition</h1>
                <p style="font-size: 15px;">is awarded for</p>
                <h2 style="font-size: 24px; color: #036635;">{$name}</h2>
                <p style="font-size: 14px; margin-top: 40px;">Given on {$issuedOn} at St. Paul University Philippines, Tuguegarao City.</p>
                <p style="margin-top: 60px; font-size: 14px;">{$adviser->name}<br><span style="font-size: 12px;">Council Adviser</span></p>
            </body></html>
            HTML)->setPaper('a4', 'landscape')->output();
    }

    /**
     * Joshua served only in completed years, so he can apply; his application waits for the admin.
     */
    private function awardApplication(): void
    {
        LeadershipAwardApplication::create([
            'user_id' => $this->person('sitestudent3@psg.com')->id,
            'award_type_id' => AwardType::where('name', 'Departmental Leadership Award')->value('id'),
            'status' => 'pending',
        ]);
    }

    private function notifications(User $adviser): void
    {
        $notes = [
            ['sitestudent4@psg.com', 'Evaluation opened', 'An evaluation you are part of is now open.', 3, false],
            ['sitestudent4@psg.com', 'Certificate issued', 'You have been issued a certificate: Outstanding Governor, PSG-SITE 2025-2026', 30, true],
            ['sitestudent4@psg.com', 'Evaluation completed', 'An evaluation you are part of has been completed.', 45, true],
            ['psgstudent1@psg.com', 'Evaluation assignment', 'You have been added to Paulinian Student Government 2026-2027.', 1, false],
            ['psgstudent1@psg.com', 'Evaluation opened', 'An evaluation you are part of is now open.', 3, true],
            ['siteadviser@psg.com', 'Evaluation submitted', 'Krisha Berbano submitted a self evaluation for Krisha Berbano.', 1, false],
            ['siteadviser@psg.com', 'Evaluation submitted', 'Biegh Alonzo submitted a peer evaluation for Krisha Berbano.', 1, false],
            ['siteadviser@psg.com', 'Evaluation submitted', 'Elijah Alonzo submitted a peer evaluation for Nathalee Bautista.', 2, true],
            ['admin@psg.com', 'New award application', 'Joshua Cabalza applied for the Departmental Leadership Award.', 2, false],
        ];

        // Written straight to the table: Filament queues its notifications, and these need their own dates and read state.
        foreach ($notes as [$email, $title, $body, $daysAgo, $read]) {
            $user = $this->person($email);
            $at = now()->subDays($daysAgo)->subHours($daysAgo * 3);

            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => FilamentDatabaseNotification::class,
                'notifiable_type' => $user->getMorphClass(),
                'notifiable_id' => $user->id,
                'data' => json_encode(Notification::make()->title($title)->body($body)->success()->getDatabaseMessage()),
                'read_at' => $read ? $at->copy()->addHours(5) : null,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }
    }

    /**
     * The sample people, with real-sounding profiles. All passwords are `password`.
     */
    private function people(): void
    {
        $site = 'School of Information Technology and Engineering';
        $saste = 'School of Art Sciences and Teacher Education';

        $people = [
            ['Luigie Cagurangan', 'siteadviser@psg.com', 'adviser', $site, '0917 555 0101', 'Faculty adviser of PSG-SITE. Guides the council\'s officers through their term and writes their adviser evaluations at the end of each year.'],
            ['Krisha Berbano', 'psgstudent1@psg.com', 'student', $site, '0917 555 0111', 'BS Information Technology student and former Governor of PSG-SITE. Runs the council\'s tech outreach program for nearby high schools.'],
            ['Yowelle Sedano', 'psgstudent2@psg.com', 'student', $saste, '0917 555 0112', 'BSEd English major. Leads the reading caravan and the campus-wide spoken word nights.'],
            ['Angel Perez', 'psgstudent3@psg.com', 'student', $saste, '0917 555 0113', 'AB Psychology student. Keeps the council\'s minutes and runs the peer counseling help desk.'],
            ['Queen Cabiao', 'psgstudent4@psg.com', 'student', $saste, '0917 555 0114', 'BSEd Mathematics major. Handles the council\'s budget and the scholarship fund drive.'],
            ['Biegh Alonzo', 'sitestudent1@psg.com', 'student', $site, '0917 555 0121', 'BS Computer Engineering student. Organizes the yearly SITE hackathon and the robotics club showcase.'],
            ['Nathalee Bautista', 'sitestudent2@psg.com', 'student', $site, '0917 555 0122', 'BS Civil Engineering student. Manages council funds and the department\'s tree planting partnership.'],
            ['Joshua Cabalza', 'sitestudent3@psg.com', 'student', $site, '0917 555 0123', 'BS Information Technology student, graduating this year. Audited the council\'s books for two terms.'],
            ['Elijah Alonzo', 'sitestudent4@psg.com', 'student', $site, '0917 555 0124', 'BS Computer Science student and Governor of PSG-SITE. Built this e-portfolio system with the council, and leads the department\'s Leaders\' Camp.'],
        ];

        foreach ($people as [$name, $email, $role, $department, $contact, $bio]) {
            $this->people[$email] = User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'role' => $role,
                'department_id' => Department::where('name', $department)->value('id'),
                'contact_number' => $contact,
                'bio' => $bio,
                'password' => 'password',
                'is_active' => true,
            ]);
        }

        $admin = User::where('role', 'admin')->firstOrFail();
        $admin->update([
            'contact_number' => '0917 555 0100',
            'bio' => 'Coordinator of the Office of Student Affairs. Keeps the councils, positions and award records of the Paulinian Student Government in order.',
        ]);
        $this->people['admin@psg.com'] = $admin;
    }

    private function backdate(Evaluation $evaluation, Carbon $when): void
    {
        $evaluation->forceFill(['created_at' => $when->copy()->subMonths(6), 'updated_at' => $when])->save();
    }

    private function person(string $email): User
    {
        return $this->people[$email] ?? throw new \RuntimeException("Demo person {$email} is missing from DatabaseSeeder.");
    }
}
