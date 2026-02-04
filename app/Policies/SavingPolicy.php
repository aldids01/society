<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Saving;
use Illuminate\Auth\Access\HandlesAuthorization;

class SavingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Saving');
    }

    public function view(AuthUser $authUser, Saving $saving): bool
    {
        return $authUser->can('View:Saving');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Saving');
    }

    public function update(AuthUser $authUser, Saving $saving): bool
    {
        return $authUser->can('Update:Saving');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Saving');
    }

    public function delete(AuthUser $authUser, Saving $saving): bool
    {
        return $authUser->can('Delete:Saving');
    }

    public function restore(AuthUser $authUser, Saving $saving): bool
    {
        return $authUser->can('Restore:Saving');
    }

    public function forceDelete(AuthUser $authUser, Saving $saving): bool
    {
        return $authUser->can('ForceDelete:Saving');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Saving');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Saving');
    }

    public function replicate(AuthUser $authUser, Saving $saving): bool
    {
        return $authUser->can('Replicate:Saving');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Saving');
    }

}