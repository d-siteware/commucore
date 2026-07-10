<?php

declare(strict_types=1);

use App\Models\Funding\Funding;
use App\Models\Project\Project;
use App\Models\User;
use App\Services\ProjectFundingReportService;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
    Storage::fake('local');
});

it('creates and stores a project executive summary document', function (): void {
    $project = Project::factory()->create(['title' => 'Jugendclub']);

    $document = app(ProjectFundingReportService::class)->createProjectReport($project, 'summary');

    expect($document->documentable_id)->toBe($project->id)
        ->and($document->documentable_type)->toBe($project::class)
        ->and($document->mime_type)->toBe('application/pdf')
        ->and($document->category)->toBe('report')
        ->and($document->original_name)->toContain('Projektbericht-summary-jugendclub');

    Storage::disk('local')->assertExists($document->path);
});

it('creates and stores a funding detailed report document', function (): void {
    $funding = Funding::factory()->create(['title' => 'Demokratie leben']);

    $document = app(ProjectFundingReportService::class)->createFundingReport($funding, 'detailed');

    expect($document->documentable_id)->toBe($funding->id)
        ->and($document->documentable_type)->toBe($funding::class)
        ->and($document->mime_type)->toBe('application/pdf')
        ->and($document->category)->toBe('usage_proof')
        ->and($document->original_name)->toContain('Foerderbericht-detail-demokratie-leben');

    Storage::disk('local')->assertExists($document->path);
});
