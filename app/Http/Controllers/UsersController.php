<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function __construct(private User $user) 
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Creating a new record in the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Validator::make(): Laravel Facade Validator static method - creating a new validator.
        // Creates a new instance of a validator for the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
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
                $role = Role::find($request->input('role_id'));
                //dd($user->getAllPermissions($role)->pluck('name'));
                //dd($role);
                //dd(Auth::user()->getAllPermissions());
                $user->assignRole($role);
                // If all these operations are successful, DB::commit() to confirm the transaction. If any error occurs, the execution passes to catch blocks.
                DB::commit();
                return response()->json($user, 201);
            } catch (Exception $e) {
                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'Internal Error'], 500);
            }
    }
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
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
                $user = Task::findOrFail($id);
                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['message' => 'Task not found'], 404);
            } catch (Exception $e) {
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
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
            'role_id' => 'sometimes|exists:roles,id',
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

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Error'], 500);

        }
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
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
            } catch (Exception $e) {

                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }        
    }
}
