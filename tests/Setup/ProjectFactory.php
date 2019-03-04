<?php  

namespace Tests\Setup;

use App\Project;
use App\User;
use App\Task;

class ProjectFactory
{
    protected $tasksCount = 0;

    protected $user;

    public function withTasks($tasksCount)
    {
        $this->tasksCount = $tasksCount;

        return $this;
    }

    public function ownedBy($user)
    {
        $this->user = $user;

        return $this;
    }

    public function create()
    {
        // $user = $this->user ?? factory(User::class)->create();

        $project = factory(Project::class)->create([
            // 'owner_id' => $user->id
            'owner_id' => $this->user ?? factory(User::class)
        ]);

        factory(Task::class, $this->tasksCount)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }
}
