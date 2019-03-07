<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // use TriggersActivity;

    protected $guarded = [];

    protected $touches = ['project'];

    protected $casts = [
        'completed' => 'boolean'
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($task) {
    //         $task->project->recordActivity('created_task');
    //     });

    //     static::deleted(function ($task) {
    //         $task->project->recordActivity('deleted_task');
    //     });
    // }

    public function complete()
    {
        $this->update(['completed' => true]);

        $this->project->recordActivity('completed_task');
    }

    public function incomplete()
    {
        $this->update(['completed' => false]);

        $this->project->recordActivity('incompleted_task');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }
}
