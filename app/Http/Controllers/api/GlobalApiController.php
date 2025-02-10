<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Office;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class GlobalApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createEmployeeResources()
    {
        $companies = Company::all();
        $divisions = Division::all();
        $offices = Office::all();
        $departments = Department::all();
        $designations = Designation::all();
        $jobTitles = JobTitle::all();
        return response()->json([
            'data' => [
                'companies' => $companies,
                'divisions' => $divisions,
                'offices' => $offices,
                'departments' => $departments,
                'designations' => $designations,
                'job_titles' => $jobTitles,
            ],
        ]);
    }

    public function firebaseTest()
    {
        try {
            $firestore = Firebase::firestore();
            $database = $firestore->database();
            $collectionReference = $database->collection('employee_locations');
            $documentReference = $collectionReference->document(10);
            $snapshot = $documentReference->snapshot();

            return response()->json([
                'message' => 'OK',
                'data' => $snapshot['address'],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }
}
