<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('layouts.app')]
#[Title('Manajemen Staf - CaffePOS')]
class UserManager extends Component
{
    use WithPagination;

    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'kasir';

    public $isEditMode = false;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'role', 'isEditMode']);
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,kasir,dapur',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = ['required', Password::defaults()];
        } elseif ($this->password) {
            $rules['password'] = [Password::defaults()];
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->isEditMode) {
            $existingUser = clone User::find($this->userId);

            if ($existingUser && $existingUser->email !== $this->email) {
                $data['provider'] = null;
                $data['provider_id'] = null;
                $data['provider_token'] = null;
            }
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->userId], $data);

        $pesan = $this->isEditMode ? 'Data staf diperbarui!' : 'Staf baru ditambahkan!';

        if ($this->isEditMode && array_key_exists('provider', $data) && $data['provider'] === null) {
            $pesan .= ' (Perhatian: Karena email diubah, tautan Sosmed staf ini telah direset otomatis demi keamanan).';
        }

        session()->flash('message', $pesan);
        $this->resetForm();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role ?? 'kasir';
        $this->password = '';
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        if (auth()->id() == $id) {
            session()->flash('error', 'Anda tidak bisa menghapus akun Anda sendiri saat sedang login!');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('message', 'Akun staf berhasil dihapus!');
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users
        ]);
    }
}
