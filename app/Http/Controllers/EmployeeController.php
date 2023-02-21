<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
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
    private const USER_NOT_FOUND = "User not found";

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
                ->join(
                    'establishments',
                    'establishments.establishment_id',
                    '=',
                    'establishments_employees.establishment_id'
                )
                ->join('users', 'users.employee_id', '=', 'employees.employee_id')
                ->select('users.*', 'employees.*', 'establishments.trade_name as establishment_name')
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
     * Store a newly created employee in storage.
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
     * Display the specified employee.
     *
     * @param  $userId
     * @return JsonResponse
     */
    public
    function show($userId): JsonResponse
    {
        try {
            $employee = DB::table('establishments_employees')
                ->join('employees', 'employees.employee_id', '=', 'establishments_employees.employee_id')
                ->join(
                    'establishments',
                    'establishments.establishment_id',
                    '=',
                    'establishments_employees.establishment_id'
                )
                ->join('users', 'users.employee_id', '=', 'employees.employee_id')
                ->select('users.*', 'employees.*', 'establishments.trade_name as establishment_name')
                ->where('users.user_id', $userId)
                ->get();

            if ($employee->isEmpty()) {
                return $this->error(null, self::NO_EMPLOYEE_FOUND, 404);
            }

            return $this->success($employee, "Employee List");

        }catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }



    /**
     * Update the specified employee in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Employee $employee
     * @return Response
     */
    public
    function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Deleting the employee ( softDelete )
     *
     * @param $userId
     * @return JsonResponse
     */
    public function destroy($userId): JsonResponse
    {
        try {

            //check if the user exist
            $user = User::withTrashed()->where('user_id', $userId)->first();
            if ($user === null) {
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }
            //check if the user is a employee
            if ($user->employee_id === null) {
                return $this->error(null, self::NO_EMPLOYEE_FOUND, 404);
            }
            //check if the user is already deleted
            if ($user->deleted_at !== null) {
                return $this->error(null, "Employee already deleted", 404);
            }
            //delete the user
            User::where('user_id', $userId)->delete();
            return $this->success(null, "Employee Deleted");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the employee
     *
     * @param $userId
     * @return JsonResponse
     */
    public function restore($userId): JsonResponse
    {
        try {
            //check if the user exist
            $user = User::withTrashed()->where('user_id', $userId)->first();
            if ($user === null) {
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }
            //check if the user is a employee
            if ($user->employee_id === null) {
                return $this->error(null, self::NO_EMPLOYEE_FOUND, 404);
            }
            //check if the user is already restored
            if ($user->deleted_at === null) {
                return $this->error(null, "Employee already restored", 404);
            }
            User::withTrashed()->where('user_id', $userId)->restore();
            return $this->success(null, "Employee Restored");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
