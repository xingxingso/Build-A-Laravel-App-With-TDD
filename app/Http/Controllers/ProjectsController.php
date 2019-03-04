<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Project;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects;
        // $projects = auth()->user()->projects()->orderBy('updated_at', 'desc')->get();

        return view('projects.index', compact('projects'));   
    }

    public function show(Project $project)
    {    
        // if (auth()->user()->isNot($project->owner)) {
        //     abort(403);
        // }

        $this->authorize('update', $project);

        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store()
    {
        // validate
        $attributes = request()->validate([
            'title' => 'required', 
            'description' => 'required',
            'notes' => 'min:3'
        ]);

        // persist
        $project = auth()->user()->projects()->create($attributes);

        // redirect   
        return redirect($project->path());
    }

    public function update(Project $project)
    {
        // if (auth()->user()->isNot($project->owner)) {
        //     abort(403);
        // }

        $this->authorize('update', $project);
        
        $project->update(request(['notes']));

        return redirect($project->path());
    }
}
