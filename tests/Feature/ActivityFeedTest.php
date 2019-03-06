<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Facades\Tests\Setup\ProjectFactory;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_project_generates_activity()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activity);

        $this->assertEquals('Created', $project->activity->first()->description);
    }

    /** @test */
    public function updated_project_generate_a_activity()
    {
        $project = ProjectFactory::create();

        $project->update(['title' => 'Changed']);

        $this->assertCount(2, $project->activity);

        $this->assertEquals('Updated', $project->activity->last()->description);   
    }
}
