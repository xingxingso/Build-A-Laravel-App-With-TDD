<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_project_can_have_tasks()
    {
        // $this->withOutExceptionHandling();

        $this->signIn();

        // $project = factory('App\Project')->create(['owner_id' => auth()->id()]);
        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $this->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
             ->assertSee('Test task');
    }

    /** @test */
    public function a_task_requires_a_body()
    {
        $this->signIn();

        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $attributes = factory('App\Task')->raw(['body' => '']); 

        $this->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');
    }
}
