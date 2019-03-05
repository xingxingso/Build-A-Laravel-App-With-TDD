<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Project;
use App\Http\Requests\UpdateProjectRequest;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects;

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
        // persist
        $project = auth()->user()->projects()->create($this->validateRequest());

        // redirect   
        return redirect($project->path());
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    // public function update(Project $project)
    // {
    //     $this->authorize('update', $project);     
    //     $project->update($this->validateRequest());
    //     return redirect($project->path());
    // }

    // public function update(UpdateProjectRequest $request, Project $project)
    // {
    //     $project->update($request->validated());
    //     return redirect($project->path());
    // }

    // public function update(UpdateProjectRequest $request, Project $project)
    // public function update(UpdateProjectRequest $request)
    public function update(UpdateProjectRequest $form)
    {    
        // $request->save();

        // return redirect($project->path());
        // return redirect($request->project()->path());
        return redirect($form->save()->path());
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required', 
            'description' => 'sometimes|required',
            // 'notes' => 'min:3'
            'notes' => 'nullable'
        ]);
    }
}
