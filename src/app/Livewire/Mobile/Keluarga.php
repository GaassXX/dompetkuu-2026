<?php

namespace App\Livewire\Mobile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Keluarga extends Component
{
    public bool $showForm = false;
    public ?int $editMemberId = null;
    public string $formName = '';
    public string $formEmail = '';
    public string $formPassword = '';

    public function getMembers()
    {
        return User::where('parent_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();
    }

    public function getStats(): array
    {
        $members = $this->getMembers();
        return [
            'total' => $members->count(),
            'active' => $members->where('is_active', true)->count(),
        ];
    }

    public function toggleActive(int $id): void
    {
        $member = User::where('parent_id', Auth::id())->findOrFail($id);
        $member->update(['is_active' => !$member->is_active]);
    }

    public function deleteMember(int $id): void
    {
        $member = User::where('parent_id', Auth::id())->findOrFail($id);
        $member->delete();
    }

    public function openAddForm(): void
    {
        $this->resetForm();
        $this->editMemberId = null;
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $member = User::where('parent_id', Auth::id())->findOrFail($id);
        $this->editMemberId = $member->id;
        $this->formName = $member->name;
        $this->formEmail = $member->email;
        $this->formPassword = '';
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'formName' => 'required|string|max:255',
            'formEmail' => $this->editMemberId
                ? 'required|email|unique:users,email,' . $this->editMemberId
                : 'required|email|unique:users,email',
            'formPassword' => $this->editMemberId ? 'nullable|string|min:8' : 'required|string|min:8',
        ]);

        $data = [
            'name' => $this->formName,
            'email' => $this->formEmail,
        ];

        if ($this->formPassword) {
            $data['password'] = Hash::make($this->formPassword);
        }

        if ($this->editMemberId) {
            $member = User::where('parent_id', Auth::id())->findOrFail($this->editMemberId);
            $member->update($data);
        } else {
            $data['parent_id'] = Auth::id();
            $data['is_active'] = true;
            $data['is_independent'] = false;
            $member = User::create($data);
            $member->assignRole('child');
        }

        $this->closeForm();
    }

    private function resetForm(): void
    {
        $this->formName = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->editMemberId = null;
    }

    public function render()
    {
        return view('livewire.mobile.keluarga', [
            'members' => $this->getMembers(),
            'stats' => $this->getStats(),
        ])->layout('layouts.mobile');
    }
}
