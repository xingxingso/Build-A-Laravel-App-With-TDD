<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    
    /** @test */
    public function only_authenticated_users_can_create_projects()
    {
        // $this->withoutExceptionHandling();
        
        $attributes = factory('App\Project')->raw();

        $this->post('/projects', $attributes)->assertRedirect('login');
    }

    /** @test */
    public function a_user_can_create_a_project()
    {   
        $this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];

        $this->post('/projects', $attributes)->assertRedirect('projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }

    /** @test */
    public function a_user_can_view_a_project()
    {
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create();

        // $this->get('/projects/' . $project->id)
        $this->get($project->path())
             ->assertSee($project->title)
             ->assertSee($project->description);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->actingAs(factory('App\User')->create());

        $attributes = factory('App\Project')->raw(['title' => '']); 

        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->actingAs(factory('App\User')->create());

        $attributes = factory('App\Project')->raw(['description' => '']); 

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

    /** @test */
    // public function a_project_requires_an_owner()
    // {
    //     // $this->withoutExceptionHandling();

    //     $attributes = factory('App\Project')->raw(['owner_id' => null]);

    //     $this->post('/projects', $attributes)->assertSessionHasErrors('owner_id');
    // }
}
