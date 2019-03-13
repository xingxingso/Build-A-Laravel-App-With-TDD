<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    //should not be protect or private
    // public $old = [];
    
    public function path()
    {
        return "/projects/{$this->id}";
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function addTask($body)
    {
        return $this->tasks()->create(compact('body'));
    }

    // public function recordActivity($description)
    // {
    //     $this->activity()->create([
    //         'description' => $description,
    //         // 'changes' => $this->activityChanges($description),
    //         'changes' => $this->activityChanges(),
    //         'project_id' => class_basename($this)==='Project' ? $this->id : $this->project_id
    //     ]);
    // }

    // // public function activityChanges($description)
    // public function activityChanges()
    // {
    //     // if ($description === 'updated') {
    //     if ($this->wasChanged()) {
    //         return [
    //             'before' => array_except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
    //             'after' => array_except($this->getChanges(), 'updated_at')
    //         ];
    //     }
    // }

    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }
}
