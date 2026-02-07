<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Aldids\FilamentDbSync\Models\DbSync;
use Illuminate\Auth\Access\HandlesAuthorization;

class DbSyncPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DbSync');
    }

    public function view(AuthUser $authUser, DbSync $dbSync): bool
    {
        return $authUser->can('View:DbSync');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DbSync');
    }

    public function update(AuthUser $authUser, DbSync $dbSync): bool
    {
        return $authUser->can('Update:DbSync');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DbSync');
    }

    public function delete(AuthUser $authUser, DbSync $dbSync): bool
    {
        return $authUser->can('Delete:DbSync');
    }

    public function restore(AuthUser $authUser, DbSync $dbSync): bool
    {
        return $authUser->can('Restore:DbSync');
    }

    public function forceDelete(AuthUser $authUser, DbSync $dbSync): bool
    {
        return $authUser->can('ForceDelete:DbSync');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DbSync');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DbSync');
    }

    public function replicate(AuthUser $authUser, DbSync $dbSync): bool
    {
        return $authUser->can('Replicate:DbSync');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DbSync');
    }

}