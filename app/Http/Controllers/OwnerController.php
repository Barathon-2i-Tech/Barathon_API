<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class OwnerController extends Controller
{
    use HttpResponses;

    private const STRINGVALIDATION = 'required|string|max:255';
    private const OWNERNOTFOUND = 'Owner not found';
    private const USERNOTFOUND = 'User not found';

    private const PHONEVALIDATION = ['regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'];

    /**
     * Store a newly created owner in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => self::STRINGVALIDATION,
            'last_name' => self::STRINGVALIDATION,
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'siren' => 'required|string|size:9|unique:owners',
            'kbis' => 'required|file|mimes:pdf|max:2048',
            'company_name' => 'nullable|string|max:255',
            'phone' => self::PHONEVALIDATION,
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.email' => "L'adresse e-mail n'est pas valide.",
            'email.unique' => "L'adresse e-mail est déjà utilisée.",
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'siren.required' => 'Le numéro SIREN est obligatoire.',
            'siren.size' => 'Le numéro SIREN doit être composé de 9 chiffres.',
            'siren.unique' => 'Le numéro SIREN est déjà utilisé.',
            'kbis.required' => 'Le KBIS est obligatoire.',
            'kbis.file' => 'Le KBIS doit être un fichier.',
            'kbis.mimes' => 'Le KBIS doit être un fichier PDF.',
            'kbis.max' => 'Le KBIS ne doit pas dépasser 2 Mo.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.regex' => 'Le numéro de téléphone n\'est pas valide.',
        ]);

        // Get the PDF file
        $kbisFile = $request->file('kbis');

        // Check if the file is valid
        if (! $kbisFile->isValid()) {
            return $this->error(null, 'Le fichier KBIS n\'est pas valide.', 500);
        }

        // Read the contents of the PDF file
        $kbisContent = file_get_contents($kbisFile->path());

        // Encode the PDF file in base64
        $kbisBase64 = 'data:application/pdf;base64,' . base64_encode($kbisContent);

        // Delete the original file
        Storage::delete($kbisFile->path());

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'avatar' => 'https://picsum.photos/180',
        ]);

        $owner = Owner::create([
            'siren' => $request->input('siren'),
            'kbis' => $kbisBase64,
            'status_id' => 3, // 3 = pending
            'phone' => $request->input('phone'),
            'company_name' => $request->input('company_name'),
        ]);

        $user->owner_id = $owner->owner_id;
        $user->save();

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
        ], 'Owner Created');
    }

    /**
     * Display the specified owner.
     */
    public function show( int $userId): JsonResponse
    {
        try {
            $owner = DB::table('users')
                ->join('owners', 'users.owner_id', '=', 'owners.owner_id')
                ->join('status', 'owners.status_id', '=', 'status.status_id')
                ->select('users.*', 'owners.*', 'status.*')
                ->where('users.user_id', $userId)
                ->get();

            if ($owner->isEmpty()) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }

            return $this->success($owner, 'Owner Details');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Update the specified owner in storage.
     */
    public function update(Request $request,  int $userId): JsonResponse
    {
        try {
            //get the user given in parameter
            $user = User::find($userId);

            if ($user === null) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }

            // check if the user is an owner
            if ($user->owner_id === null) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }

            // validate the request
            $request->validate([
                'first_name' => self::STRINGVALIDATION,
                'last_name' => self::STRINGVALIDATION,
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users')->ignore($user), // Ignore the user given in parameter
                ],
                'company_name' => 'nullable|string|max:255',
                'phone' => self::PHONEVALIDATION,
            ], [
                'first_name.required' => 'Le prénom est obligatoire.',
                'last_name.required' => 'Le nom est obligatoire.',
                'email.required' => "L'adresse e-mail est obligatoire.",
                'email.email' => "L'adresse e-mail n'est pas valide.",
                'email.unique' => "L'adresse e-mail est déjà utilisée.",
                'phone.required' => 'Le numéro de téléphone est obligatoire.',
                'phone.regex' => 'Le numéro de téléphone n\'est pas valide.',
            ]);

            $user->fill($request->only(['first_name', 'last_name', 'email']));
            $user->save();

            // Update the owner data
            $owner = Owner::find($user->owner_id);
            $owner->fill($request->only(['company_name', 'phone']));
            $owner->save();

            $userChanges = $user->getChanges();
            $ownerChanges = $owner->getChanges();

            if (empty($userChanges) && empty($ownerChanges)) {
                return $this->success(null, 'Owner not updated');
            }

            return $this->success([$user, $owner], 'Owner Updated');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the owner ( softDelete )
     */
    public function destroy( int $userId): JsonResponse
    {
        try {
            //check if the user exist
            $user = User::withTrashed()->where('user_id', $userId)->first();
            if ($user === null) {
                return $this->error(null, self::USERNOTFOUND, 404);
            }
            //check if the user is an owner
            if ($user->owner_id === null) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }
            //check if the user is already deleted
            if ($user->deleted_at !== null) {
                return $this->error(null, 'Owner already deleted', 404);
            }
            //delete the user
            User::where('user_id', $userId)->delete();

            return $this->success(null, 'Owner Deleted');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the owner
     */
    public function restore( int $userId): JsonResponse
    {
        try {
            //check if the user exist
            $user = User::withTrashed()->where('user_id', $userId)->first();
            if ($user === null) {
                return $this->error(null, self::USERNOTFOUND, 404);
            }
            //check if the user is an owner
            if ($user->owner_id === null) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }
            //check if the user is already restored
            if ($user->deleted_at === null) {
                return $this->error(null, 'Owner already restored', 404);
            }
            User::withTrashed()->where('user_id', $userId)->restore();

            return $this->success(null, 'Owner Restored');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display a listing of all owners
     */
    public function getOwnerList(): JsonResponse
    {
        try {
            $owners = DB::table('users')
                ->join('owners', 'users.owner_id', '=', 'owners.owner_id')
                ->join('status', 'owners.status_id', '=', 'status.status_id')
                ->select('users.*', 'owners.*', 'status.status_id', 'status.comment')
                ->get();

            if ($owners->isEmpty()) {
                return $this->error(null, 'No owners found', 404);
            }

            return $this->success($owners, 'Owner List');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Validate the owner
     */
    public function validateOwner( int $ownerId,  int $statusCode): jsonResponse
    {
        try {
            $owner = Owner::find($ownerId);

            if (! $owner) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }

            if ($owner->status_id === $statusCode) {
                return $this->error(null, 'Owner already validated', 404);
            }

            $owner->status_id = $statusCode;
            $owner->save();

            return $this->success(null, 'Validation updated');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Get how many owner need to be validated
     */
    public function getOwnerToValidate(): JsonResponse
    {
        try {
            $ownerToValidate = Owner::where('status_id', 3)->count();

            return $this->success($ownerToValidate, 'Owner to validate');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
