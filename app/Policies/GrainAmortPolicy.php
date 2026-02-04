<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GrainAmort;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrainAmortPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GrainAmort');
    }

    public function view(AuthUser $authUser, GrainAmort $grainAmort): bool
    {
        return $authUser->can('View:GrainAmort');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GrainAmort');
    }

    public function update(AuthUser $authUser, GrainAmort $grainAmort): bool
    {
        return $authUser->can('Update:GrainAmort');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GrainAmort');
    }

    public function delete(AuthUser $authUser, GrainAmort $grainAmort): bool
    {
        return $authUser->can('Delete:GrainAmort');
    }

    public function restore(AuthUser $authUser, GrainAmort $grainAmort): bool
    {
        return $authUser->can('Restore:GrainAmort');
    }

    public function forceDelete(AuthUser $authUser, GrainAmort $grainAmort): bool
    {
        return $authUser->can('ForceDelete:GrainAmort');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GrainAmort');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GrainAmort');
    }

    public function replicate(AuthUser $authUser, GrainAmort $grainAmort): bool
    {
        return $authUser->can('Replicate:GrainAmort');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GrainAmort');
    }

}