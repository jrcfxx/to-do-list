<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskChangeController extends Controller
{

    /**
     * TaskChangeController constructor.
     * Initializes the controller with the TaskChange model instance.
     *
     * @param TaskChange $taskChange TaskChange model instance.
     */

    public function __construct(private TaskChange $taskChange) 
    {
    }

    /**
     * Display a listing of the TaskChanges.
     * Fetches and returns all TaskChange records from the database.
     *
     * @return \Illuminate\Http\Response JSON response containing all TaskChanges.
     */
    public function index()
    {
        return TaskChange::all();
    }

    /**
     * Create a new TaskChange record in the database.
     * Validates the input data and stores the new TaskChange.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing data for the new TaskChange.
     * @return \Illuminate\Http\Response JSON response containing the newly created TaskChange or validation errors.
     */
    public function create(Request $request)
    {
         // Validator::make(): Laravel Facade Validator static method - creating a new validator.
        // Creates a new instance of a validator for the request data
        $validator = Validator::make($request->all(), [
            'task_id' => 'sometimes|required|exists:task,id',
            'changed_field' => 'required|string',
            'old_value' => 'required|string',
            'new_value' => 'required|string',
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
                $taskChange = $this->$taskChange->create($request->all());
                // If all these operations are successful, DB::commit() to confirm the transaction. If any error occurs, the execution passes to catch blocks.
                DB::commit();
                return response()->json($taskChange,201);
            } catch (\Exception $e) {
                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }
    }

    /**
     * Display the specified TaskChange.
     * Fetches and returns a specific TaskChange record by its ID.
     *
     * @param int $id The ID of the TaskChange to retrieve.
     * @return \Illuminate\Http\Response JSON response containing the TaskChange or an error message.
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
                $taskChange = Task::findOrFail($id);
                return response()->json($task, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['message' => 'TaskChange not found'], 404);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }
    }

    /**
     * Update the specified TaskChange in the database.
     * Validates and updates a specific TaskChange record by its ID.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing updated data for the TaskChange.
     * @param int $id The ID of the TaskChange to update.
     * @return \Illuminate\Http\Response JSON response containing the updated TaskChange or an error message.
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'sometimes|required|exists:task,id',
            'changed_field' => 'required|string',
            'old_value' => 'required|string',
            'new_value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Bad Request', 'messages' => $validator->errors()], 400);
        } else {
            DB::beginTransaction();
        try {
            $taskChange = Task::findOrFail($id);
            $taskChange->update($request->all());

            DB::commit();
            return response()->json($taskChange, 200);

        } catch (ModelNotFoundException $e) {

            DB::rollBack();
            return response()->json(['message' => 'TaskChange not found'], 404);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['message' => 'Internal Error'], 500);

        }
    }
    }
}
