<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Credential;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Credential $credential)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Credential $credential)
    {
        // $credential = Auth::user();
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('view_company', $permissions);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('add_company', $permissions);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create2(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('add_company', $permissions);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('edit_company', $permissions);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('delete_company', $permissions);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Credential $credential, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Credential $credential, Company $company)
    {
        //
    }
}
