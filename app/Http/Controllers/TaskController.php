<?php

namespace App\Http\Controllers;

use App\Events\TaskCreated;
use App\Models\Task;
use App\Notifications\TaskCreatedNotification;
use App\Rules\ValidTaskStatus;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json([
            'status' => 'success',
            'data' => $tasks,
            'message' => 'Tasks retrieved successfully',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => ['required', new ValidTaskStatus],
        ]);

        // Create a new task
        $task = Task::create([
            'title' => $request->input('title'),
            'status' => $request->input('status'),
        ]);

        // Log the activity
        $this->logActivity("Task created: " . $task->title);

        // Fire an event for task creation
        event(new TaskCreated($task));

        // Notify the user (if logged in)
        $user = auth()->user();
        $user->notify(new TaskCreatedNotification($task));

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $task,
            'message' => 'Task retrieved successfully',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => ['required', new ValidTaskStatus],
        ]);

        // Find the task and update it
        $task = Task::findOrFail($id);
        $task->update([
            'title' => $request->input('title'),
            'status' => $request->input('status'),
        ]);

        // Log the activity
        $this->logActivity("Task updated: " . $task->title);

        return response()->json([
            'status' => 'success',
            'data' => $task,
            'message' => 'Task updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        $this->logActivity("Task deleted: " . $task->title);

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully',
        ]);
    }
}
