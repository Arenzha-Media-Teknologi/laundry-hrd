<?php

namespace App\Policies;

use App\Models\Credential;
use App\Models\DailySalary;
use Illuminate\Auth\Access\HandlesAuthorization;

class DailySalaryPolicy
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
        return in_array('view_magenta_daily_salary', $permissions);
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
        return in_array('add_magenta_daily_salary', $permissions);
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
        return in_array('edit_magenta_daily_salary', $permissions);
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
        return in_array('delete_magenta_daily_salary', $permissions);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\DailySalary  $dailySalary
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Credential $credential, DailySalary $dailySalary)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Credential  $credential
     * @param  \App\Models\DailySalary  $dailySalary
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Credential $credential, DailySalary $dailySalary)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAerplus(Credential $credential)
    {
        // $credential = Auth::user();
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('view_aerplus_daily_salary', $permissions);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createAerplus(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('add_aerplus_daily_salary', $permissions);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateAerplus(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('edit_aerplus_daily_salary', $permissions);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Credential  $credential
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAerplus(Credential $credential)
    {
        $groupPermissions = $credential->group->permissions ?? "[]";
        $permissions = json_decode($groupPermissions);
        return in_array('delete_aerplus_daily_salary', $permissions);
    }
}
