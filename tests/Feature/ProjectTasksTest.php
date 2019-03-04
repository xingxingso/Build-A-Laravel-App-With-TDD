<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
// use Tests\Setup\ProjectFactory;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_add_tasks_to_projects()
    {
        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

    /** @test */
    public function only_the_owner_of_a_project_may_add_tasks()
    // public function adding_a_task_if_you_are_not_the_project_owner()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
             ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }

    /** @test */
    public function only_the_owner_of_a_project_may_update_a_task()
    {
        $this->signIn();

        // $project = factory('App\Project')->create();
        // $task = $project->addTask('test task');

        $project = ProjectFactory::withTasks(1)->create();

        // $this->patch($task->path(), ['body' => 'changed'])
        $this->patch($project->tasks[0]->path(), ['body' => 'changed'])
             ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        // $this->signIn();

        // $project = auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );

        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
             ->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
             ->assertSee('Test task');
    }

    /** @test */
    public function a_task_can_be_updated()
    {
        // $this->withOutExceptionHandling();

        // $project = app(ProjectFactory::class)
        //     // ->ownedBy($this->signIn())
        //     ->withTasks(1)
        //     ->create();

        $project = ProjectFactory::withTasks(1)->create();

        // $project = auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );

        // $task = $project->addTask('test task');

        $this->actingAs($project->owner)
        // $this->patch($task->path(), [
            ->patch($project->tasks->first()->path(), [
            'body' => 'changed',
            'completed' => true
        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => true
        ]);
    }

    /** @test */
    public function a_task_requires_a_body()
    {
        // $this->signIn();

        // $project = auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );

        $project = ProjectFactory::create();

        $attributes = factory('App\Task')->raw(['body' => '']); 

        $this->actingAs($project->owner)
             ->post($project->path() . '/tasks', $attributes)
             ->assertSessionHasErrors('body');
    }
}
