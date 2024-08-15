<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct(
        private User $user,
        private UsersController $userController
    ) 
    {
        $this->userController = $userController;
    }


    public function register(Request $request)
    {
        //Assigns the UsersController instance to the $userController property - available in other methods of the class
        return $this->userController->create($request);
    }

    public function login(Request $request)
    {
        // Validate the request 
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {

            // If the validation fails, it returns a JSON response with an error message and the status HTTP 400 (Bad Request).
            // 'messages' => $validator->errors(): The detailed error messages provided by the validator. 
            // errors() returns an associative array with fields that failed validation and their error messages.
            return response()->json(['error' => 'Bad Request', 'messages' => $validator->errors()], 400);

        } else {
            try {
                // Search for a user in the database whose email address matches the one provided in the request.
                $user = User::where('email', $request->email)->first();

                // Check if the user was found and if the provided password matches the stored password.
                if (! $user || ! Hash::check($request->password, $user->password)) {
                    // Returns a JSON response with an error message and HTTP 401 (Unauthorized) status
                    return response()->json(['error' => 'Unauthorized', 'message' => 'The provided credentials are incorrect.'], 401);
                } else {
                    // Create an authentication token for the user.
                    // 'authToken' - name of the token.
                    // 'plainTextToken' is the generated token in plain text.
                    $token = $user->createToken('authToken')->plainTextToken;

                    //$permissions = $user->getAllPermissions(); // get all user permissions
                    //dump($permissions); // showing the permissions without interrupting the execution

                    //$role = Role::find($request->input('role_id'));
                    //dd($user->getAllPermissions($role)->pluck('name'));
                    return response()->json(['token' => $token], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }
}

    public function logout(Request $request)
    {

        // Deletes the user's current access token, logging them out.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

}
