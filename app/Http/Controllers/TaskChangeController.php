<?php

namespace App\Http\Controllers;

use App\Models\TaskChange;
use Illuminate\Http\Request;

class TaskChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskChange::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'sometimes|required|exists:task,id',
            'change_date' => 'required|date',
            'changed_field' => 'required',
            'changed_content' => 'required',
        ]);

         return TaskChange::create([
            'task_id' => $request->name,
            'change_date' => $request->change_date,
            'changed_field' => $request->changed_field,
            'changed_content' => $request->changed_content,
         ]);


        return response()->json($taskchange, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $taskchange)
    {
        return response()->json($taskchange);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaskChange $taskchange)
    {
        $request->validate([
            'task_id' => 'sometimes|required|exists:task,id',
            'change_date' => 'required|date',
            'changed_field' => 'required',
            'changed_content' => 'required',
        ]);  

        $taskchange->update($request->all());

        return response()->json($taskchange);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $taskchange)
    {
        $taskchange->update(['change_date' => now()]);
        return response()->json(null, 204);
    }
}
