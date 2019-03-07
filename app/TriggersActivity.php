<?php
/**
 * if use it currently, the tests will not work
 * you should change the `description` like 'created_project',
 * on `task`, should add `task_id` or change the method `activity`.
 */

namespace App;

trait TriggersActivity
{
    /**
     * Boot the trait.
     * named boot[TraitName], 
     * it will be executed as the boot() function would on an Eloquent model.
     */
    // protected static function bootRecordsActivity() //didn't work `boot[TraitName]`
    protected static function bootTriggersActivity()
    {
        foreach (static::getModelEventsToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity(
                    $model->formatActivityDescription($event)
                );
            });
        }
    }

    /**
     * Record activity for the model.
     * 
     * @param   [type]     $description [description]
     */
    public function recordActivity($description)
    {     
        $this
            ->activitySubject()
            ->activity()
            ->create(compact('description'));
    }

    /**
     * The activity feed for the project.
     * 
     * @return  [type]     [description]
     */
    public function activity()
    {
        return $this->hasMany(Activity::class);   
    }

    /**
     * Get the subject for the activity recording
     * 
     * @return  $this
     */
    protected function activitySubject()
    {
        return $this;
    }

    /**
     * Get the model events that should trigger activity recording.
     * 
     * @return  [type]     [description]
     */
    protected static function getModelEventsToRecord()
    {
        if (isset(static::$modelEventsToRecord)) {
            return static::$modelEventsToRecord;
        }

        return ['created', 'updated', 'deleted'];
    }

    /**
     * Format the activity description.
     *
     * @param string $event
     * @return string
     */
    protected function formatActivityDescription($event)
    {
        return "{$event}_" . strtolower(class_basename($this));
    }
}
