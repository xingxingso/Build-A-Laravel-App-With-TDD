<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Project;
use App\Task;

class ProjectTasksController extends Controller
{
    public function store(Project $project)
    {
        $this->authorize('update', $project);

        request()->validate(['body' => 'required']);

        $project->addTask(request('body'));

        return redirect($project->path());
    }

    public function update(Project $project, Task $task)
    {
        $this->authorize('update', $task->project);

        // request()->validate(['body' => 'required']);

        // $task->update(['body' => request('body')]);

        $task->update(request()->validate(['body' => 'required']));

        // if (request()->has('completed')) {
        //     $task->complete();
        // }

        // if (request('completed')) {
        //     $task->complete();
        // } else {
        //     $task->incomplete();
        // }

        $method = request('completed') ? 'complete' : 'incomplete';
        // $task->{$method}();
        $task->$method();

        return redirect($project->path());
    }
}
