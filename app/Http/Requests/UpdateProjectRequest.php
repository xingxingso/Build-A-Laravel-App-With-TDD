<?php

namespace App\Http\Requests;

use App\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return Gate::allows('update', $this->route('project'));
        return Gate::allows('update', $this->project());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|required', 
            'description' => 'sometimes|required',
            'notes' => 'nullable'
        ];
    }

    public function project()
    {
        // return $this->route('project');

        // dd(Project::findOrFail($this->route('project')));
        // dd($this->route('project'));

        // use this `Project::findOrFail($this->route('project'))` will cann't use `public function update(UpdateProjectRequest $request, Project $project)` in controller, if, 403 will response.
        // because if in the parameters has `Project $project`,
        // `Project::findOrFail($this->route('project'))` will return array
        // if not, `$this->route('project')` will be "1" (the id of table projects)
        // and `Project::findOrFail($this->route('project'))` will be an object of database collection
        return Project::findOrFail($this->route('project'));
    }

    public function save()
    {
        // $this->project()->update($this->validated());

        // $project = $this->project();

        // $project->update($this->validated());

        // return $project;

        return tap($this->project())->update($this->validated());
    }
}
