<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Task::with('tester','project','developer')->get();
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|string',
            // 'assigned_date' => 'required|date_format:Y-m-d',
            'developer_id'=>'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        $taskImages = [];
        if ($request->hasFile('task_images')) {
            foreach ($request->file('task_images') as $image) {
                $path = $image->store('task_images', 'public');
                $taskImages[] = $path;
            }
        }
        // dd($taskImages);

        $bug = Task::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            // 'tester_id' => $request->input('tester_id'),
            'task_images' => json_encode($taskImages),
            'developer_id' => $request->input('developer_id'),
            'status' => $request->input('status'),
            'project_id' => $request->input('project_id'),
        ]);

        return response()->json($bug->load('developer','tester','project'), 201);
       
    }
    public function assignTesters(Request $request, $id)
    {
        // dd($request->all());
        $this->validate($request, [
            'tester_id' => 'required|exists:users,id',   
        ]);
        $bug = Task::findOrFail($id);
        $bug->tester_id=$request->input('tester_id');
        $bug->save();
        return response()->json($bug->load('developer','tester','project'), 200);
    }
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Bug deleted successfully'], 200);
    }
    public function show($id)
    {
        return Task::with( 'developer','tester','project')->findOrFail($id);
    }
    public function updateTask(Request $request, $id)
    {
        dd($request->task_images);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'project_id' => 'nullable|string',
            'task_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $bug = Task::findOrFail($id);

        // Update bug details
        $bug->name = $request->input('name');
        $bug->description = $request->input('description');
        $bug->status = $request->input('status');
        $bug->project_id = $request->input('project_id');

        // Handle bug images
        if ($request->hasFile('task_images')) {
            $bugImages = [];
            foreach ($request->file('task_images') as $image) {
                $path = $image->store('task_images', 'public');
                $bugImages[] = $path;
            }
            $bug->task_images = $bugImages;
        }

        $bug->save();

        return response()->json($bug->load('developer','tester','project'), 200);
    }
    public function getTaskForDeveloper($developerId)
    {
        $data= Task::with('developer','tester','project')
            ->whereHas('tester', function($query) use ($developerId) {
                $query->where('tester_id', $developerId);
            })
            ->get();
         return response()->json($data);   
    }
    public function updateStatus(Request $request, $id)
{
    $bug = Task::findOrFail($id);
    $previousStatus = $bug->status;

    // $bug->name = $request->input('name');
    // $bug->description = $request->input('description');
    $bug->status = $request->input('status');
    // other updates...

    $bug->save();

    // if ($previousStatus !== $bug->status && in_array($bug->status, ['working', 'closed'])) {
    //     event(new BugStatusChanged($bug));
    // }

    return response()->json($bug->load('developer','tester','project'), 200);
}
}
