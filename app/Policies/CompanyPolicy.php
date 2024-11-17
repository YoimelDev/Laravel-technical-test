<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use LaravelJsonApi\Core\Store\LazyRelation;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view companies');
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        return $user->can('view companies');
    }

    /**
     * Determine whether the user can create companies.
     */
    public function create(User $user): bool
    {
        return $user->can('create companies');
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->can('edit companies');
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        return $user->can('delete companies');
    }

    /**
     * Check if user is admin or company owner
     */
    private function canManageActivityTypes(User $user, Company $company): bool
    {
        return $user->hasRole('admin') || $company->user_id === $user->id;
    }

    /**
     * Authorize a user to attach activity types to the company.
     *
     * @param User $user
     * @param Company $company
     * @param LazyRelation $relation
     * @return bool
     */
    public function attachActivityTypes(
        User $user,
        Company $company,
        LazyRelation $relation
    ): bool {
        return $this->canManageActivityTypes($user, $company);
    }

    /**
     * Authorize a user to detach activity types from the company.
     *
     * @param User $user
     * @param Company $company
     * @param LazyRelation $relation
     * @return bool
     */
    public function detachActivityTypes(
        User $user,
        Company $company,
        LazyRelation $relation
    ): bool {
        return $this->canManageActivityTypes($user, $company);
    }
}
