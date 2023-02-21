<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    use HttpResponses;
    private const NO_EMPLOYEE_FOUND = "No employee found";

    /**
     * Display a listing of all employee.
     *
     * @return JsonResponse
     */
    public function getEmployeeList(): JsonResponse
    {
        try {
            $employees = DB::table('establishments_employees')
                ->join('employees', 'employees.employee_id', '=', 'establishments_employees.employee_id')
                ->join('establishments', 'establishments.establishment_id', '=', 'establishments_employees.establishment_id')
                ->join('users', 'users.employee_id', '=', 'employees.employee_id')
                ->select('users.*', 'employees.*','establishments.trade_name as establishment_name')
                ->get();

            if ($employees->isEmpty()) {
                return $this->error(null, self::NO_EMPLOYEE_FOUND, 404);
            }

            return $this->success($employees, "Employee List");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
        /**
         * Show the form for creating a new resource.
         *
         * @return Response
         */
        public
        function create()
        {
            //
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return Response
         */
        public
        function store(Request $request)
        {
            //
        }

        /**
         * Display the specified resource.
         *
         * @param \App\Models\Employee $employee
         * @return Response
         */
        public
        function show(Employee $employee)
        {
            //
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param \App\Models\Employee $employee
         * @return Response
         */
        public
        function edit(Employee $employee)
        {
            //
        }

        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\Models\Employee $employee
         * @return Response
         */
        public
        function update(Request $request, Employee $employee)
        {
            //
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param \App\Models\Employee $employee
         * @return Response
         */
        public
        function destroy(Employee $employee)
        {
            //
        }
}
