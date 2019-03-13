<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $touches = ['project'];

    protected $casts = [
        'completed' => 'boolean'
    ];

    // public $old = [];

    static $recordableEvents = ['created', 'deleted'];
    
    public function complete()
    {
        $this->update(['completed' => true]);

        $this->recordActivity('completed_task');
    }

    public function incomplete()
    {
        $this->update(['completed' => false]);

        $this->recordActivity('incompleted_task');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }

    // public function recordActivity($description)
    // {
    //     $this->activity()->create([
    //         'description' => $description,
    //         'changes' => $this->activityChanges($description),
    //         // 'project_id' => $this->project_id
    //         'project_id' => class_basename($this)==='Project' ? $this->id : $this->project_id
    //     ]);
    // }

    // public function activityChanges()
    // {
    //     return null;
    //     if ($this->wasChanged()) {
    //         return [
    //             'before' => array_except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
    //             'after' => array_except($this->getChanges(), 'updated_at')
    //         ];
    //     }
    // }

    // public function activity()
    // {
    //     return $this->morphMany(Activity::class, 'subject')->latest();
    // }
}
