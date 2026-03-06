<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Member;

use App\Actions\Member\CreateRole;
use App\Actions\Member\UpdateRole;
use App\Models\Locale;
use App\Models\Membership\Role;
use App\Rules\UniqueJsonSlug;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class RoleForm extends Form
{
    protected Role $role;

    public int $id;

    #[Validate]
    public array $name;

    public string $description = '';

    public int $sort = 0;

    public bool $can_manage_accounting = false;

    public bool $can_represent_organization = false;

    public function set(int $roleId): void
    {

        try {
            $this->role = Role::query()->findOrFail($roleId);
            $this->name = $this->role->name;
            $this->description = $this->role->description;
            $this->sort = $this->role->sort;
            $this->can_manage_accounting = $this->role->can_manage_accounting;
            $this->can_represent_organization = $this->role->can_represent_organization;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

    }

    public function create(): Role
    {
        $this->validate();

        if (Role::query()->count() === 0) {
            $this->sort = 0;
        } else {
            $this->sort = Role::query()
                ->max('sort') + 1;
        }

        return CreateRole::handle($this);
    }

    public function update(): Role
    {
        $this->validate();

        return UpdateRole::handle($this, $this->role);
    }

    protected function rules(): array
    {
        $locales = Locale::available();

        $rules = [
            'description' => 'nullable|string',
            'sort' => 'integer|min:0',
            'can_manage_accounting' => 'boolean',
            'can_represent_organization' => 'boolean',
        ];

        foreach ($locales as $locale) {
            $rules["name.{$locale}"] = ['required', 'string', new UniqueJsonSlug('roles', 'name')];
        }

        return $rules;

    }

    protected function messages(): array
    {
        return [

        ];
    }
}
