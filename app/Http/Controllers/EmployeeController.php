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
            ], [
                'first_name.required' => 'The first name is required',
                'first_name.string' => 'The first name must be a string',
                'first_name.max' => 'The first name must be less than 255 characters',
                'last_name.required' => 'The last name is required',
                'last_name.string' => 'The last name must be a string',
                'last_name.max' => 'The last name must be less than 255 characters',
                'email.required' => 'The email is required',
                'email.string' => 'The email must be a string',
                'email.email' => 'The email must be a valid email',
                'email.unique' => 'The email must be unique',
                'password.required' => 'The password is required',
                'password.confirmed' => 'The password confirmation does not match',
                'hiring_date.required' => 'The hiring date is required',
                'hiring_date.date' => 'The hiring date must be a valid date',
            ]);

            //create the user
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'avatar' => 'https://picsum.photos/180',
            ]);

            //create the employee
            $employee = Employee::create([
                'hiring_date' => $request->input('hiring_date'),
            ]);
            //link the user to the employee
            $user->employee_id = $employee->employee_id;

            //check if the establishment exists
            $establishment = Establishment::find($request->input('establishment_id'));
            if (!$establishment) {
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

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display the specified employee.
     */
    public function show( $userId): JsonResponse
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

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request,  $userId): JsonResponse
    {
        try {

            $user = User::find($userId);
            if ($user === null) {
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }

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
            ], [
                    'first_name.required' => 'The first name is required',
                    'first_name.string' => 'The first name must be a string',
                    'first_name.max' => 'The first name must be less than 255 characters',
                    'last_name.required' => 'The last name is required',
                    'last_name.string' => 'The last name must be a string',
                    'last_name.max' => 'The last name must be less than 255 characters',
                    'email.required' => 'The email is required',
                    'email.string' => 'The email must be a string',
                    'email.email' => 'The email must be a valid email',
                    'email.unique' => 'The email must be unique',
                ]
            );

            $user->fill($request->only(['first_name', 'last_name', 'email']));
            $user->save();

            $userChanges = $user->getChanges();

            if (empty($userChanges)) {
                return $this->success($user, 'Employee not updated');
            }

            return $this->success($user, 'Employee Updated', 200);
        } catch (Exception $error) {

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the employee ( softDelete )
     */
    public function destroy( $userId): JsonResponse
    {
        try {

            $user = User::withTrashed()
                ->where('user_id', $userId)
                ->whereNotNull('employee_id')
                ->first();

            if (!$user) {
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }

            if ($user->deleted_at !== null) {
                return $this->error(null, 'Employee already deleted', 404);
            }

            $user->delete();
            return $this->success(null, 'Employee Deleted');

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the employee
     */
    public function restore( $userId): JsonResponse
    {
        try {
            $user = User::withTrashed()
                ->where('user_id', $userId)
                ->whereNotNull('employee_id')
                ->first();

            if (!$user) {
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }

            if ($user->deleted_at === null) {
                return $this->error(null, 'Employee already restored', 404);
            }

            $user->restore();

            return $this->success(null, 'Employee Restored');
        } catch (Exception $error) {

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
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
