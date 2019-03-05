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
        // $attributes = $this->validateRequest();

        // persist
        $project = auth()->user()->projects()->create($this->validateRequest());

        // redirect   
        return redirect($project->path());
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Project $project)
    {
        $this->authorize('update', $project);
            
        // $attributes = $this->validateRequest();

        $project->update($this->validateRequest());

        return redirect($project->path());
    }

    public function validateRequest()
    {
        return request()->validate([
            'title' => 'required', 
            'description' => 'required',
            'notes' => 'min:3'
        ]);
    }
}
