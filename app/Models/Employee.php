<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * Guarded
     */
    protected $guarded = [];

    /**
     * Appends
     */
    // protected $appends = ['working_pattern'];

    /**
     * Get the employee's name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // protected function number(): Attribute
    // {
    // $companyInitial = $this->office?->division?->company?->initial;
    // $divisionInitial = $this->office?->division?->initial;
    // $initials = $companyInitial . '-' . $divisionInitial;

    //     return Attribute::make(
    //         get: fn ($value) => 'EMPLOYEE-NUMBER-001',
    //     );
    // }

    /**
     * Set attribute new health
     */
    // public function getNewHealthAttribute()
    // {
    //     return $this->health;
    // }

    /**
     * Set attribute working pattern
     */
    public function getWorkingPatternAttribute()
    {
        return $this->activeWorkingPattern()->with(['items'])->first();
    }

    /**
     * Get Careers
     */
    public function careers()
    {
        return $this->hasMany(Career::class);
    }

    /**
     * Get Careers
     */
    public function activeCareer()
    {
        return $this->hasOne(Career::class)->where('active', 1);
    }

    /**
     * Get Office
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get salary components
     */
    public function salaryComponents()
    {
        return $this->belongsToMany(SalaryComponent::class)->withPivot('amount', 'coefficient', 'effective_date');
    }

    /**
     * Get insurances
     */
    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }

    /**
     * Get working patterns
     */
    public function workingPatterns()
    {
        return $this->belongsToMany(WorkingPattern::class)->withPivot('active');
    }

    /**
     * Get active working pattern
     */
    public function activeWorkingPattern()
    {
        return $this->workingPatterns()->wherePivot('active', true);
    }

    /**
     * Get active working patterns
     */
    public function activeWorkingPatterns()
    {
        return $this->workingPatterns()->wherePivot('active', true);
    }

    /**
     * Get attendances
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get loans
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Leave
     */
    public function leave()
    {
        return $this->hasOne(Leave::class);
    }

    /**
     * Salaries
     */
    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * Daily Salaries
     */
    public function dailySalaries()
    {
        return $this->hasMany(DailySalary::class);
    }

    /**
     * Bank Accounts
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Leave Applications
     */
    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    /**
     * Sick Applications
     */
    public function sickApplications()
    {
        return $this->hasMany(SickApplication::class);
    }

    /**
     * Permission Applications
     */
    public function permissionApplications()
    {
        return $this->hasMany(PermissionApplication::class);
    }

    /**
     * Employee Files
     */
    public function files()
    {
        return $this->hasMany(EmployeeFile::class);
    }

    /**
     * BPJS
     */
    public function bpjs()
    {
        return $this->hasOne(EmployeeBpjs::class);
    }

    /**
     * Private Insurance
     */
    public function privateInsurances()
    {
        return $this->belongsToMany(PrivateInsurance::class)->withPivot('number', 'start_year', 'card_image');
    }

    /**
     * BPJS Value
     */
    public function bpjsValues()
    {
        return $this->hasMany(BpjsValue::class);
    }

    /**
     * Private Insurance Value
     */
    public function privateInsuranceValues()
    {
        return $this->hasMany(PrivateInsuranceValue::class);
    }

    /**
     * Drivers License
     */
    public function driversLicenses()
    {
        return $this->hasMany(DriversLicense::class);
    }

    /**
     * Credential
     */
    public function credential()
    {
        return $this->hasOne(Credential::class);
    }

    /**
     * Deposit
     */
    public function salaryDeposits()
    {
        return $this->hasMany(SalaryDeposit::class);
    }
}
