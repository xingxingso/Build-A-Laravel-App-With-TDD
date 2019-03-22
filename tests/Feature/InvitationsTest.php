<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Facades\Tests\Setup\ProjectFactory;
use App\User;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_project_can_invite_a_user()
    {
        $this->withOutExceptionHandling();

        // Given I have a project
        $project = ProjectFactory::create();

        // And the owner of the project invites another user
        $project->invite($newUser = factory(User::class)->create());

        // Then, that new user will have permission to add tasks
        $this->signIn($newUser);

        $this->post(action('ProjectTasksController@store', $project), $task = ['body' => 'add a new task']);

        $this->assertDatabaseHas('tasks', $task);
    }
}
