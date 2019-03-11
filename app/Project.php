<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // use TriggersActivity;

    protected $guarded = [];

    //should not be protect or private
    public $old = [];

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

    public function recordActivity($description)
    {
        // $this->activity()->create(compact('description'));

        // var_dump($this->old, $this->toArray());
        
        $this->activity()->create([
            'description' => $description,
            // 'changes' => $description === 'updated' ? [
            //     // 'before' => array_diff($this->old, $this->toArray()),
            //     // 'after' => array_diff($this->toArray(), $this->old)

            //     // 'before' => array_diff($this->old, $this->getAttributes()),
            //     // 'after' => array_diff($this->getAttributes(), $this->old)

            //     'before' => array_diff($this->old, $this->getAttributes()),
            //     'after' => $this->getChanges()
            // ] : null
            'changes' => $this->activityChanges($description)
        ]);
    }

    public function activityChanges($description)
    {
        if ($description === 'updated') {
            return [
                // 'before' => array_diff($this->old, $this->getAttributes()),
                // 'after' => $this->getChanges()
                'before' => array_except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
                'after' => array_except($this->getChanges(), 'updated_at')
            ];
        }
    }

    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }
}
