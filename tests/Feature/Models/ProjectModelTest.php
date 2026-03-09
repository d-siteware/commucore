<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;
use App\Livewire\Activity\Project\Create\Page as ProjectCreatePage;
use App\Livewire\Activity\Project\Index\Page as ProjectIndexPage;
use App\Livewire\Activity\Project\Show\Page as ProjectShowPage;
use App\Models\Funding\Funding;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->admin()->create();
    $this->actingAs($this->user);
});

// =============================================================================
// Model
// =============================================================================

describe('Project Model', function (): void {

    it('has correct fillable attributes', function (): void {
        $project = Project::factory()->create([
            'title' => 'Jugendclub 2025',
            'status' => ProjectStatus::Active,
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);

        expect($project->title)->toBe('Jugendclub 2025')
            ->and($project->status)->toBe(ProjectStatus::Active)
            ->and($project->start_date)->toBeInstanceOf(\Carbon\Carbon::class)
            ->and($project->end_date)->toBeInstanceOf(\Carbon\Carbon::class);
    });

    it('has projectTransactions relation', function (): void {
        $project = Project::factory()->create();
        ProjectTransaction::factory()->create(['project_id' => $project->id]);

        expect($project->projectTransactions)->toHaveCount(1);
    });

    it('has fundings relation with pivot', function (): void {
        $project = Project::factory()->create();
        $funding = Funding::factory()->create();
        $project->fundings()->attach($funding->id, ['allocated_amount' => 1_000_00]);

        expect($project->fundings)->toHaveCount(1)
            ->and($project->fundings->first()->pivot->allocated_amount)->toBe(1_000_00);
    });

    it('calculates totalFundingAllocated correctly', function (): void {
        $project = Project::factory()->create();
        $funding1 = Funding::factory()->create();
        $funding2 = Funding::factory()->create();

        $project->fundings()->attach($funding1->id, ['allocated_amount' => 1_000_00]);
        $project->fundings()->attach($funding2->id, ['allocated_amount' => 500_00]);

        expect($project->totalFundingAllocated())->toBe(1_500_00);
    });

    it('scopeActive returns only active projects', function (): void {
        Project::factory()->create(['status' => ProjectStatus::Active]);
        Project::factory()->create(['status' => ProjectStatus::Planned]);
        Project::factory()->create(['status' => ProjectStatus::Completed]);

        expect(Project::active()->count())->toBe(1);
    });

    it('scopeInYear returns projects active in given year', function (): void {
        Project::factory()->create([
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);
        Project::factory()->create([
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        expect(Project::inYear(2025)->count())->toBe(1);
    });

});

// =============================================================================
// ProjectStatus Enum
// =============================================================================

describe('ProjectStatus Enum', function (): void {

    it('has correct labels', function (): void {
        expect(ProjectStatus::Planned->label())->toBeString()
            ->and(ProjectStatus::Active->label())->toBeString()
            ->and(ProjectStatus::Completed->label())->toBeString()
            ->and(ProjectStatus::Cancelled->label())->toBeString();
    });

    it('has correct colors', function (): void {
        expect(ProjectStatus::Active->color())->toBe('green')
            ->and(ProjectStatus::Cancelled->color())->toBe('red')
            ->and(ProjectStatus::Planned->color())->toBe('gray')
            ->and(ProjectStatus::Completed->color())->toBe('indigo');
    });

    it('returns options array with all cases', function (): void {
        $options = ProjectStatus::options();
        expect($options)->toHaveCount(4)
            ->and($options)->toHaveKey('planned')
            ->and($options)->toHaveKey('active');
    });

});

// =============================================================================
// Index Page
// =============================================================================

describe('Project Index Page', function (): void {

    it('renders successfully', function (): void {
        Livewire::test(ProjectIndexPage::class)
            ->assertOk();
    });

    it('shows projects in table', function (): void {
        Project::factory()->create([
            'title' => 'Testprojekt',
            'status' => ProjectStatus::Active,
        ]);

        Livewire::test(ProjectIndexPage::class)
            ->set('filteredBy', [ProjectStatus::Active->value])
            ->assertSee('Testprojekt');
    });

    it('filters by status', function (): void {
        Project::factory()->create(['title' => 'Aktives Projekt', 'status' => ProjectStatus::Active]);
        Project::factory()->create(['title' => 'Abgebrochenes Projekt', 'status' => ProjectStatus::Cancelled]);

        Livewire::test(ProjectIndexPage::class)
            ->set('filteredBy', [ProjectStatus::Active->value])
            ->assertSee('Aktives Projekt')
            ->assertDontSee('Abgebrochenes Projekt');
    });

    it('searches by title', function (): void {
        Project::factory()->create(['title' => 'Jugendclub', 'status' => ProjectStatus::Active]);
        Project::factory()->create(['title' => 'Sportprojekt', 'status' => ProjectStatus::Active]);

        Livewire::test(ProjectIndexPage::class)
            ->set('filteredBy', [ProjectStatus::Active->value])
            ->set('search', 'Jugend')
            ->assertSee('Jugendclub')
            ->assertDontSee('Sportprojekt');
    });

    it('sorts by title', function (): void {
        Project::factory()->create(['title' => 'Zebra', 'status' => ProjectStatus::Active]);
        Project::factory()->create(['title' => 'Alpha', 'status' => ProjectStatus::Active]);

        $component = Livewire::test(ProjectIndexPage::class)
            ->set('filteredBy', [ProjectStatus::Active->value])
            ->call('sort', 'title');

        expect($component->get('sortBy'))->toBe('title');
    });

});

// =============================================================================
// Create Page
// =============================================================================

describe('Project Create Page', function (): void {

    it('renders successfully', function (): void {
        Livewire::test(ProjectCreatePage::class)
            ->assertOk();
    });

    it('pre-selects Planned status on mount', function (): void {
        Livewire::test(ProjectCreatePage::class)
            ->assertSet('form.status', ProjectStatus::Planned->value);
    });

    it('creates a project and redirects to show page', function (): void {
        Livewire::test(ProjectCreatePage::class)
            ->set('form.title', 'Neues Testprojekt')
            ->set('form.status', ProjectStatus::Active->value)
            ->set('form.start_date', '2025-01-01')
            ->set('form.end_date', '2025-12-31')
            ->call('createProject')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'title' => 'Neues Testprojekt',
            'status' => ProjectStatus::Active->value,
        ]);
    });

    it('validates required title', function (): void {
        Livewire::test(ProjectCreatePage::class)
            ->set('form.status', ProjectStatus::Planned->value)
            ->call('createProject')
            ->assertHasErrors(['form.title' => 'required']);
    });

    it('validates required status', function (): void {
        Livewire::test(ProjectCreatePage::class)
            ->set('form.title', 'Test')
            ->set('form.status', '')
            ->call('createProject')
            ->assertHasErrors(['form.status' => 'required']);
    });

    it('validates end_date after start_date', function (): void {
        Livewire::test(ProjectCreatePage::class)
            ->set('form.title', 'Test')
            ->set('form.status', ProjectStatus::Planned->value)
            ->set('form.start_date', '2025-12-31')
            ->set('form.end_date', '2025-01-01')
            ->call('createProject')
            ->assertHasErrors(['form.end_date']);
    });

    it('allows optional fields to be empty', function (): void {
        Livewire::test(ProjectCreatePage::class)
            ->set('form.title', 'Minimales Projekt')
            ->set('form.status', ProjectStatus::Planned->value)
            ->call('createProject')
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'title' => 'Minimales Projekt',
            'start_date' => null,
            'end_date' => null,
        ]);
    });

});

// =============================================================================
// Show Page
// =============================================================================

describe('Project Show Page', function (): void {

    it('renders successfully', function (): void {
        $project = Project::factory()->create();

        Livewire::test(ProjectShowPage::class, ['project' => $project])
            ->assertOk();
    });

    it('displays project title in subheading', function (): void {
        $project = Project::factory()->create(['title' => 'Sichtbares Projekt']);

        Livewire::test(ProjectShowPage::class, ['project' => $project])
            ->assertSee('Sichtbares Projekt');
    });

    it('updates project data', function (): void {
        $project = Project::factory()->create([
            'title' => 'Alter Titel',
            'status' => ProjectStatus::Planned,
        ]);

        Livewire::test(ProjectShowPage::class, ['project' => $project])
            ->set('form.title', 'Neuer Titel')
            ->set('form.status', ProjectStatus::Active->value)
            ->call('updateProject');

        expect($project->fresh()->title)->toBe('Neuer Titel')
            ->and($project->fresh()->status)->toBe(ProjectStatus::Active);
    });

    it('calculates fundingAllocated correctly', function (): void {
        $project = Project::factory()->create();
        $funding = Funding::factory()->create();
        $project->fundings()->attach($funding->id, ['allocated_amount' => 2_000_00]);

        $component = Livewire::test(ProjectShowPage::class, ['project' => $project]);

        expect($component->get('fundingAllocated'))->toBe(2_000_00);
    });

    it('deletes project and redirects to index', function (): void {
        $project = Project::factory()->create();

        Livewire::test(ProjectShowPage::class, ['project' => $project])
            ->call('deleteProject')
            ->assertRedirect(route('project.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    });

});
