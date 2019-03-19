<?php  

namespace App;

/**
* record activity trait
*/
trait RecordsActivity
{
    public $oldAttributes = [];

    /**
     * [boot the trait]
     */
    public static function bootRecordsActivity()
    {
        foreach (static::recordableEvents() as $event) { 
            static::$event(function ($model) use ($event) {
                $model->recordActivity($model->activityDescription($event));
            });

            if ($event === 'updated') {
                static::updating(function ($model) {
                    $model->oldAttributes = $model->getOriginal();
                });
            }
        }
    }

    protected function activityDescription($description)
    {
        return "${description}_" . strtolower(class_basename($this));    
    }

    protected static function recordableEvents()
    {
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        } 
        // return ['created', 'updated', 'deleted'];
        return ['created', 'updated'];
    }

    public function recordActivity($description)
    {
        $this->activity()->create([
            'user_id' => ($this->project ?? $this)->owner->id,
            'description' => $description,
            'changes' => $this->activityChanges($description),
            'project_id' => class_basename($this)==='Project' ? $this->id : $this->project_id
        ]);
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function activityChanges()
    {
        if ($this->wasChanged()) {
            return [
                'before' => array_except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'),
                'after' => array_except($this->getChanges(), 'updated_at')
            ];
        }
    }
}
