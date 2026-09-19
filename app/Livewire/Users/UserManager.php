<?php

namespace App\Livewire\Users;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = 'all';

    public bool $isModalOpen = false;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'Cashier';

    public ?int $outlet_id = null;

    public bool $is_active = true;

    public function openCreateModal(): void
    {
        $this->reset(['userId', 'name', 'email', 'password']);
        $this->role = 'Cashier';
        $this->outlet_id = Outlet::first()?->id;
        $this->is_active = true;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->roles->first()?->name ?? 'Cashier';
        $this->outlet_id = $user->outlet_id;
        $this->is_active = (bool) $user->is_active;

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->userId],
            'role' => ['required', 'exists:roles,name'],
            'outlet_id' => ['nullable', 'exists:outlets,id'],
            'is_active' => ['boolean'],
        ];

        if (! $this->userId) {
            $rules['password'] = ['required', 'string', 'min:6'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:6'];
        }

        $this->validate($rules);

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'outlet_id' => $this->outlet_id,
                'is_active' => $this->is_active,
            ];
            if (! empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            $user->syncRoles([$this->role]);

            session()->flash('success', "Pengguna '{$this->name}' berhasil diperbarui.");
        } else {
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'outlet_id' => $this->outlet_id,
                'is_active' => $this->is_active,
            ]);
            $user->assignRole($this->role);

            session()->flash('success', "Pengguna '{$this->name}' berhasil ditambahkan.");
        }

        $this->isModalOpen = false;
        $this->reset(['userId', 'name', 'email', 'password']);
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun sendiri!');

            return;
        }

        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('success', "Pengguna '{$user->name}' berhasil dihapus.");
    }

    public function render(): View
    {
        $users = User::query()
            ->with(['roles', 'outlet'])
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when($this->roleFilter !== 'all', function ($query): void {
                $query->role($this->roleFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.users.user-manager', [
            'users' => $users,
            'roles' => Role::all(),
            'outlets' => Outlet::where('is_active', true)->get(),
        ]);
    }
}
