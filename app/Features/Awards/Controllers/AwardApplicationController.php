<?php

namespace App\Features\Awards\Controllers;

use App\Features\Awards\Actions\ApplyForLeadershipAward;
use App\Features\Awards\Models\AwardType;
use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class AwardApplicationController
{
    public function store(Request $request, ApplyForLeadershipAward $apply): RedirectResponse
    {
        $data = $request->validate([
            'award_type_id' => ['required', 'exists:award_types,id'],
            'graduating' => ['accepted'],
        ], [
            'graduating.accepted' => 'Confirm that you are graduating to apply.',
        ]);

        $awardType = AwardType::findOrFail($data['award_type_id']);

        try {
            $apply($request->user(), $awardType);
        } catch (EvaluationRuleException $e) {
            Inertia::flash('error', $e->getMessage());

            return back();
        }

        Inertia::flash('success', "Your application for the {$awardType->name} is in. The admin will review it.");

        return back();
    }
}
