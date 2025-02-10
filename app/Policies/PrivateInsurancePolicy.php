<?php

namespace App\Policies;

use App\Models\Credential;
use App\Models\PrivateInsurance;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivateInsurancePolicy
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
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Credential $credential)
    {
        // $credential = Auth::user();
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('view_private_insurance', $permissions);
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
        return in_array('add_private_insurance', $permissions);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('edit_private_insurance', $permissions);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('delete_private_insurance', $permissions);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\PrivateInsurance  $privateInsurance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Credential $credential, PrivateInsurance $privateInsurance)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\PrivateInsurance  $privateInsurance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Credential $credential, PrivateInsurance $privateInsurance)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewValue(Credential $credential)
    {
        // $credential = Auth::user();
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('view_insurance_value', $permissions);
    }
}
