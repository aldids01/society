<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LoanAmort;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanAmortPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LoanAmort');
    }

    public function view(AuthUser $authUser, LoanAmort $loanAmort): bool
    {
        return $authUser->can('View:LoanAmort');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LoanAmort');
    }

    public function update(AuthUser $authUser, LoanAmort $loanAmort): bool
    {
        return $authUser->can('Update:LoanAmort');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LoanAmort');
    }

    public function delete(AuthUser $authUser, LoanAmort $loanAmort): bool
    {
        return $authUser->can('Delete:LoanAmort');
    }

    public function restore(AuthUser $authUser, LoanAmort $loanAmort): bool
    {
        return $authUser->can('Restore:LoanAmort');
    }

    public function forceDelete(AuthUser $authUser, LoanAmort $loanAmort): bool
    {
        return $authUser->can('ForceDelete:LoanAmort');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LoanAmort');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LoanAmort');
    }

    public function replicate(AuthUser $authUser, LoanAmort $loanAmort): bool
    {
        return $authUser->can('Replicate:LoanAmort');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LoanAmort');
    }

}