<?php

declare(strict_types=1);

use App\Models\Membership\MemberApplication;

test('non-auth besucher kann den eigenen mitgliedsantrag per token abrufen', function (): void {
    $application = MemberApplication::factory()->create();

    $response = $this->get("/members/print-member-application/{$application->token}");

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('fremder token wird abgewiesen', function (): void {
    $application = MemberApplication::factory()->create();

    $this->get('/members/print-member-application/ungueltiger-token')
        ->assertNotFound();

    $this->get("/members/print-member-application/{$application->token}")
        ->assertOk();
});

test('token ist nicht per id-enumeration abrufbar', function (): void {
    $application = MemberApplication::factory()->create();

    // Die Route nimmt einen Token entgegen, keine numerische ID.
    // Ein Versuch, die ID zu enumerieren, schlägt fehl.
    $this->get('/members/print-member-application/1')
        ->assertNotFound();

    $this->get("/members/print-member-application/{$application->token}")
        ->assertOk();
});
