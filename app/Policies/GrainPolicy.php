<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Grain;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrainPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Grain');
    }

    public function view(AuthUser $authUser, Grain $grain): bool
    {
        return $authUser->can('View:Grain');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Grain');
    }

    public function update(AuthUser $authUser, Grain $grain): bool
    {
        return $authUser->can('Update:Grain');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Grain');
    }

    public function delete(AuthUser $authUser, Grain $grain): bool
    {
        return $authUser->can('Delete:Grain');
    }

    public function restore(AuthUser $authUser, Grain $grain): bool
    {
        return $authUser->can('Restore:Grain');
    }

    public function forceDelete(AuthUser $authUser, Grain $grain): bool
    {
        return $authUser->can('ForceDelete:Grain');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Grain');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Grain');
    }

    public function replicate(AuthUser $authUser, Grain $grain): bool
    {
        return $authUser->can('Replicate:Grain');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Grain');
    }

}