<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Establishment;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    use HttpResponses;

    private const NO_EMPLOYEE_FOUND = 'No employee found';
    private const USER_NOT_FOUND = 'User not found';
    private const STRINGVALIDATION = 'required|string|max:255';

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): JsonResponse
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
                'avatar' => 'https://picsum.photos/180',
            ]);

            //create the employee
            $employee = Employee::create([
                'hiring_date' => $request->hiring_date,
            ]);
            //link the user to the employee
            $user->employee_id = $employee->employee_id;

            //check if the establishment exists
            $establishment = Establishment::find($request->establishment_id);
            if (! $establishment) {
                return $this->error(null, 'Establishment not found', 404);
            }

            // attach the employee to the establishment with the belonging table
            $establishment->employees()->attach($employee->employee_id);

            //save the user
            $user->save();

            return $this->success([
                'userLogged' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
            ], 'Employee Created', 201);
        } catch (Exception $error) {
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display the specified employee.
     */
    public function show($userId): JsonResponse
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

            return $this->success($employee, 'Employee');
        } catch (Exception $error) {
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  Employee  $employee
     * @return Response
     */
    public function update(Request $request, $userId): JsonResponse
    {
        try {
            // Get the user given in parameter
            $user = User::find($userId);
            if ($user === null) {
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }

            // Check if the user is a employee
            if ($user->employee_id === null) {
                return $this->error(null, self::NO_EMPLOYEE_FOUND, 404);
            }

            $request->validate([
                'first_name' => self::STRINGVALIDATION,
                'last_name' => self::STRINGVALIDATION,
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users')->ignore($user), // Ignore the user given in parameter
                ],
            ]);

            $userData = $request->only(['first_name', 'last_name', 'email']);
            // Check if the data given in parameter are different from the data in database
            foreach ($userData as $field => $value) {
                if ($user->{$field} !== $value) {
                    $user->{$field} = $value;
                }
            }
            $user->save();

            $userChanges = $user->getChanges();

            // Check if the user data has changed
            if (empty($userChanges)) {
                return $this->success($user, 'Employee not updated');
            }

            return $this->success($user, 'Employee Updated', 200);
        } catch (Exception $error) {
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the employee ( softDelete )
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
                return $this->error(null, 'Employee already deleted', 404);
            }
            //delete the user
            User::where('user_id', $userId)->delete();

            return $this->success(null, 'Employee Deleted');
        } catch (Exception $error) {
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display a listing of all employee.
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

            return $this->success($employees, 'Employees List');
        } catch (Exception $error) {
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the employee
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
                return $this->error(null, 'Employee already restored', 404);
            }
            User::withTrashed()->where('user_id', $userId)->restore();

            return $this->success(null, 'Employee Restored');
        } catch (Exception $error) {
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
