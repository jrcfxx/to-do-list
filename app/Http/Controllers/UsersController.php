<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;


class UsersController extends Controller
{

    /**
     * UsersController constructor.
     * Initializes the controller with the User model instance.
     *
     * @param User $user The User model instance.
     */
    public function __construct(private User $user) 
    {
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing all users.
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Create a new user record in the database.
     * Validates the input data and stores the new user.
     *
     * @param Request $request The HTTP request object containing user data.
     * @return \Illuminate\Http\JsonResponse The JSON response with the created user or error message.
     */
    public function create(Request $request)
    {
        // Validator::make(): Laravel Facade Validator static method - creating a new validator.
        // Creates a new instance of a validator for the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Returns true if validation fails and false if it passes. Checks that the data provided does not meet the defined validation rules
        if ($validator->fails()) {

            // If the validation fails, it returns a JSON response with an error message and the status HTTP 400 (Bad Request).
            // 'messages' => $validator->errors(): The detailed error messages provided by the validator. 
            // errors() returns an associative array with fields that failed validation and their error messages.
            return response()->json(['error' => 'Bad Request', 'messages' => $validator->errors()], 400);

        } else {
            try {
                // All database operations after this line will be treated as a single work unit.
                DB::beginTransaction();
                $user = $this->user->create($request->all());
                $role = Role::where('name', 'user')->firstOrFail();
                //dd($user->getAllPermissions($role)->pluck('name'));
                //dd($role);
                //dd(Auth::user()->getAllPermissions());
                $user->assignRole($role);
                // If all these operations are successful, DB::commit() to confirm the transaction. If any error occurs, the execution passes to catch blocks.
                DB::commit();
                return response()->json($user, 201);
            } catch (\Exception $e) {
                // Rollback the transaction
                DB::rollBack();
                return response()->json([
                    'message' => 'Internal Error',
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString(),
                ], 500);
            }
    }
}

    /**
     * Display the specified user.
     * Retrieves and returns a single user by their ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return \Illuminate\Http\JsonResponse The JSON response with the user data or error message.
     */
    public function show(int $id)
    {
        // Validator::make(): Laravel Facade Validator static method - creating a new validator.
        // Creates a new instance of a validator for the request data
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer'
        ]);

        // Returns true if validation fails and false if it passes. Checks that the data provided does not meet the defined validation rules
        if ($validator->fails()) {
            return response()->json(['error' => 'Bad Request', 'messages' => $validator->errors()], 400);
        } else {
            try {
                $user = User::findOrFail($id);
                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['message' => 'User not found'], 404);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }
    }

    /**
     * Update the specified user in the database.
     * Validates the input data and updates the user record.
     *
     * @param Request $request The HTTP request object containing updated user data.
     * @param int $id The ID of the user to update.
     * @return \Illuminate\Http\JsonResponse The JSON response with the updated user or error message.
     */
    public function update(Request $request, int $id)
    {

        $user = User::findOrFail($id);
        // Validator::make(): Laravel Facade Validator static method - creating a new validator.
        // Creates a new instance of a validator for the request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            // $user->id Checks if the email is unique in all records except for the record with id equal to $user->id
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:6',
        ]);

        // Returns true if validation fails and false if it passes. Checks that the data provided does not meet the defined validation rules
        if ($validator->fails()) {
            // If the validation fails, it returns a JSON response with an error message and the status HTTP 400 (Bad Request).
            // 'messages' => $validator->errors(): The detailed error messages provided by the validator. 
            // errors() returns an associative array with fields that failed validation and their error messages.
            return response()->json(['error' => 'Bad Request', 'messages' => $validator->errors()], 400);
        } else {
            // All database operations after this line will be treated as a single work unit.
            DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());

            DB::commit();
            return response()->json($user, 200);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Task not found'], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Error'], 500);

        }
    }
    }

    /**
     * Remove the specified user from the database.
     * Deletes the user record.
     *
     * @param int $id The ID of the user to delete.
     * @return \Illuminate\Http\JsonResponse The JSON response confirming deletion or error message.
     */
    public function delete(int $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Bad Request', 'messages' => $validator->errors()], 400);
        } else {
            try {
                // All database operations after this line will be treated as a single work unit.
                DB::beginTransaction();

                // Searches the task by ID. If it is not found, throws a ModelNotFoundException exception.
                $user = $this->user->findOrFail($id);
                $user->delete();

                // If all these operations are successful, DB::commit() to confirm the transaction. If any error occurs, the execution passes to catch blocks.
                DB::commit();
    
                return response()->json(['message' => 'User deleted'], 204);
            } catch (ModelNotFoundException $e) {

                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'User not found'], 404);
            } catch (\Exception $e) {

                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }        
    }
}
