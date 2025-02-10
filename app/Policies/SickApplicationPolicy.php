<?php

namespace App\Policies;

use App\Models\Credential;
use App\Models\SickApplication;
use Illuminate\Auth\Access\HandlesAuthorization;

class SickApplicationPolicy
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
        return in_array('view_sick_application', $permissions);
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
        return in_array('add_sick_application', $permissions);
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
        return in_array('edit_sick_application', $permissions);
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
        return in_array('delete_sick_application', $permissions);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\SickApplication  $sickApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Credential $credential, SickApplication $sickApplication)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\SickApplication  $sickApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Credential $credential, SickApplication $sickApplication)
    {
        //
    }
}
