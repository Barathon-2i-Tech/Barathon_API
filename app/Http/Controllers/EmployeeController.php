<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Establishment;
use App\Models\Establishment_Employee;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    use HttpResponses;

    private const NO_EMPLOYEE_FOUND = "No employee found";
    private const USER_NOT_FOUND = "User not found";
    private const STRINGVALIDATION = 'required|string|max:255';

    /**
     * Display a listing of all employee.
     *
     * @return JsonResponse
     */
    public function getEmployeeList(): JsonResponse
    {
        try {
            $employees = DB::table('users')
                ->join('employees', 'users.employee_id', '=', 'employees.employee_id')
                ->join('establishments_employees', 'employees.employee_id', '=', 'establishments_employees.employee_id')
                ->join(
                    'establishments',
                    'establishments_employees.establishment_id',
                    '=',
                    'establishments.establishment_id'
                )
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
     * @return JsonResponse
     */
    public
    function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => self::STRINGVALIDATION,
                'last_name' => self::STRINGVALIDATION,
                'email' => 'required|string|email|unique:users',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'hiring_date' => 'required|date',
            ]);

            //create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'avatar' => "https://picsum.photos/180",
            ]);
            //create the employee
            $employee = Employee::create([
                'hiring_date' => $request->hiring_date,
            ]);
            //link the user to the employee
            $user->employee_id = $employee->employee_id;

            //check if the establishment exists
            $establishment = Establishment::find($request->establishment_id);
            if (!$establishment) {
                return $this->error(null, "Establishment not found", 404);
            }

            // attach the employee to the establishment with the belonging table
            $establishment->employees()->attach($employee->employee_id);

            //save the user
            $user->save();

            return $this->success([
                'userLogged' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ], "Employee Created", 201);

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
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

        } catch (Exception $error) {
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
