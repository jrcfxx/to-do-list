<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class TaskController extends Controller
{

     /**
     * TaskController constructor.
     * Initializes the controller with the Task and TaskChange model instances.
     *
     * @param Task $task Task model instance.
     * @param TaskChange $taskChange TaskChange model instance.
     */

    public function __construct(
        private Task $task,
        private TaskChange $taskChange
    ) 
    {
    }

    /**
     * Display a listing of the Tasks.
     * Fetches and returns all Task records from the database.
     *
     * @return \Illuminate\Http\Response JSON response containing all Tasks.
     */
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Filters the tasks by user_id
        $tasks = Task::where('user_id', $user->id)->get();

        return response()->json($tasks, 200);
    }

    /**
     * Create a new Task record in the database.
     * Validates the input data and creates the new Task.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing data for the new Task.
     * @return \Illuminate\Http\JsonResponse JSON response containing the newly created Task or validation errors.
     */
    public function create(Request $request)
    {

        $userId = $request->user()->id;

        // Validator::make(): Laravel Facade Validator static method - creating a new validator.
        // Creates a new instance of a validator for the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|integer',
            'status' => 'required|string|max:255',
            'due_date' => 'required|date',
            'completeness_date' => 'sometimes|nullable|date',
            'delete_date' => 'sometimes|nullable|date',
        ]);

        // Returns true if validation fails and false if it passes. Checks that the data provided does not meet the defined validation rules
        if ($validator->fails()) {

            // If the validation fails, it returns a JSON response with an error message and the status HTTP 400 (Bad Request).
            // 'messages' => $validator->errors(): The detailed error messages provided by the validator. 
            // errors() returns an associative array with fields that failed validation and their error messages.
            return response()->json(['error' => 'Bad Request', 'messages' => $validator->errors()], 400);

        } else {
            try {
                DB::beginTransaction();

                // Creates the new task with the merged data - adding the user_id automatically
                $task = $this->task->create(array_merge(
                    $request->all(),
                    ['user_id' => $userId]
                ));

                // If all these operations are successful, DB::commit() to confirm the transaction. If any error occurs, the execution passes to catch blocks.
                DB::commit();
                return response()->json($task, 201);
            } catch (Exception $e) {
                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'Internal Error'], 500);
            }
    }
}

    /**
     * Display the specified Task.
     * Fetches and returns a specific Task record by its ID.
     *
     * @param  int  $id The ID of the Task to retrieve.
     * @return \Illuminate\Http\Response JSON response containing the Task or an error message.
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
                $task = Task::findOrFail($id);
                return response()->json($task, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['message' => 'Task not found'], 404);
            } catch (Exception $e) {
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }
    }

    /**
     * Update the specified Task in storage.
     * Validates and updates a specific Task record by its ID.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request containing updated data for the Task.
     * @param  int  $id The ID of the Task to update.
     * @return \Illuminate\Http\Response JSON response containing the updated Task or an error message.
     */
    public function update(Request $request, int $id)
    {
        $messages = [
            'completeness_date.before_or_equal' => 'The completeness date cannot be in the future.',
        ];

        // Validator::make(): Laravel Facade Validator static method - creating a new validator.
        // Creates a new instance of a validator for the request data
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'sometimes|required|integer',
            'status' => 'required|string|max:255',
            'due_date' => 'sometimes|required|date',
            'completeness_date' => 'nullable|date|before_or_equal:today', // Ensure that the completion date is not future
            'delete_date' => 'nullable|date',
        ], $messages);

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
            
            $task = Task::findOrFail($id);
            // The Eloquent getOriginal() method returns the values of attributes before any update.
            $originalTask = $task->getOriginal();
            $task->update($request->all());

            // After updating the task, the getChanges() method captures the attributes that have been changed. 
            // This method returns an associative array where the keys are the names of the fields that have been modified and the values are the new values of those fields.
            $changes = $task->getChanges();
            // For each field that has been modified, a corresponding record is created in the TaskChange table
            // $field represents the name of the field that has been changed
            // $newValue: represents the new value that this field took after updating
            // The loop allows to process each change individually, capturing both the field name ($field) and the new value of that field ($newValue).
            foreach ($changes as $field => $newValue) {
                // $originalTask[$field]: returns the value that this field had before updating
                $oldValue = $originalTask[$field] ?? ''; // Ensures that the old value is empty if it does not exist
                $this->taskChange->create([
                    'task_id' => $task->id,
                    'changed_field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ]);
            }

            DB::commit();
            return response()->json($task, 200);
        } catch (ModelNotFoundException $e) {

            // Rollback the transaction
            DB::rollBack();
            return response()->json(['message' => 'Task not found'], 404);
        } catch (Exception $e) {

            // Rollback the transaction
            DB::rollBack();
            return response()->json(['message' => 'Internal Error'], 500);
        }
    }
    }

    /**
     * Remove the specified task from the database.
     * Deletes the task record and logs the deletion.
     *
     * @param int $id The ID of the task to delete.
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
                $task = $this->task->findOrFail($id);

                $this->taskChange->create([
                    'task_id' => $task->id,
                    'changed_field' => 'deleted',
                    'old_value' => 'deleted',
                    'new_value' => 'deleted',
                ]);

                $task->delete();

                // If all these operations are successful, DB::commit() to confirm the transaction. If any error occurs, the execution passes to catch blocks.
                DB::commit();
    
                return response()->json(['message' => 'Task deleted'], 200);
            } catch (ModelNotFoundException $e) {

                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'Task not found'], 404);
            } catch (Exception $e) {

                // Rollback the transaction
                DB::rollBack();
                return response()->json(['message' => 'Internal Error'], 500);
            }
        }        
    }
}
