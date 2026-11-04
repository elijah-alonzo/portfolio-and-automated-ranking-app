<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('renders the landing page with live stats', function () {
    $this->withoutVite();

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('stats.activeCouncils', 0)
            ->where('stats.ongoingEvaluations', 0)
            ->where('stats.studentOfficers', 0));
});
