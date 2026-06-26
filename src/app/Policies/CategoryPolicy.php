<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Category $category): bool
    {
        return true;
    }

    public function delete(User $user, Category $category): bool
    {
        return true;
    }

    public function deleteAny(User $user): bool
    {
        return true;
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Category $category): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Category $category): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
