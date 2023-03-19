<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Establishment;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                'first_name.required' => 'Le prénom est obligatoire.',
                'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
                'first_name.max' => 'Le prénom doit faire moins de 255 caractères.',
                'last_name.required' => 'Le nom est obligatoire.',
                'last_name.string' => 'Le nom doit être une chaîne de caractères.',
                'last_name.max' => 'Le nom doit faire moins de 255 caractères.',
                'email.required' => 'L\'adresse e-mail est obligatoire.',
                'email.string' => 'L\'adresse e-mail doit être une chaîne de caractères.',
                'email.email' => 'L\'adresse e-mail n\'est pas valide.',
                'email.unique' => 'L\'adresse e-mail est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'hiring_date.required' => 'La date d\'embauche est obligatoire.',
                'hiring_date.date' => 'La date d\'embauche doit être une date valide.',
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
    public function show( int $userId): JsonResponse
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
    public function update(Request $request, int $userId): JsonResponse
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
                    'first_name.required' => 'Le prénom est obligatoire.',
                    'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
                    'first_name.max' => 'Le prénom doit faire moins de 255 caractères.',
                    'last_name.required' => 'Le nom est obligatoire.',
                    'last_name.string' => 'Le nom doit être une chaîne de caractères.',
                    'last_name.max' => 'Le nom doit faire moins de 255 caractères.',
                    'email.required' => 'L\'adresse e-mail est obligatoire.',
                    'email.string' => 'L\'adresse e-mail doit être une chaîne de caractères.',
                    'email.email' => 'L\'adresse e-mail n\'est pas valide.',
                    'email.unique' => 'L\'adresse e-mail est déjà utilisée.',
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
    public function destroy( int $userId): JsonResponse
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
    public function restore( int $userId): JsonResponse
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
