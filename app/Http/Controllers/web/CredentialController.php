<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CredentialController extends Controller
{
    /**
     * Generate SQL INSERT queries for credentials table
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateSqlQueries(Request $request)
    {
        // Get all employees or filter by employee_id if provided
        $employeeId = $request->input('employee_id');

        if ($employeeId) {
            $employees = Employee::where('id', $employeeId)->get();
        } else {
            // Get all employees that don't have credentials yet
            $employees = Employee::whereDoesntHave('credential')->get();
        }

        $sqlQueries = [];
        $hashedPassword = Hash::make('12345678');

        foreach ($employees as $employee) {
            // Get first word from name
            $nameParts = explode(' ', trim($employee->name));
            $firstName = !empty($nameParts[0]) ? $nameParts[0] : 'user';

            // Remove special characters and convert to lowercase
            $firstName = preg_replace('/[^a-zA-Z0-9]/', '', $firstName);
            $firstName = strtolower($firstName);

            // If firstName is empty after cleaning, use 'user' as default
            if (empty($firstName)) {
                $firstName = 'user';
            }

            // Generate unique username: firstname + employee_id
            $username = $firstName . $employee->id;

            // Escape values for SQL
            $escapedUsername = DB::getPdo()->quote($username);
            $escapedPassword = DB::getPdo()->quote($hashedPassword);
            $empId = (int) $employee->id;

            // Generate SQL INSERT query
            $sql = "INSERT INTO `credentials` (`id`, `username`, `password`, `mobile_access`, `employee_id`, `credential_group_id`, `created_at`, `updated_at`) VALUES (NULL, {$escapedUsername}, {$escapedPassword}, 1, {$empId}, 1, NOW(), NOW());";

            $sqlQueries[] = $sql;
        }

        // Return as plain text with SQL queries
        $output = implode("\n", $sqlQueries);

        return response($output, 200)
            ->header('Content-Type', 'text/plain');
    }
}
