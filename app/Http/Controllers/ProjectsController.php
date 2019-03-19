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

    public function update(UpdateProjectRequest $form)
    {    
        return redirect($form->save()->path());
    }

    public function destroy(Project $project)
    {
        $this->authorize('update', $project);

        $project->delete();

        return redirect('/projects');
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required', 
            'description' => 'sometimes|required',
            'notes' => 'nullable'
        ]);
    }
}
