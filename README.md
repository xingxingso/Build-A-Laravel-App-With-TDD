[TOC]

# [Build-A-Laravel-App-With-TDD](https://laracasts.com/series/build-a-laravel-app-with-tdd)

> It's time to take the techniques we learned in Laravel From Scratch, and put them to good use building your first real-world application. Together, we'll leverage TDD to create Birdboard: a minimal Basecamp-like project management app.  This series will give us a wide range of opportunities to pull up our sleeves and test our Laravel chops. As always, we start from scratch: laravel new birdboard.

## 01. [Meet Birdboard](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/1)

> Let's begin by reviewing the application that we plan to build. We'll then finish up by installing Laravel and performing the first commit.

## 02. [Let's Begin With a Test](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/2)

> My hope is to demonstrate, as much as possible, my actual workflow when writing my own applications. With that in mind, let's begin with our first feature test.

### Note

> tests\Feature\ProjectsTest.php

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_project()
    {   
        $this->withoutExceptionHandling();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];

        $this->post('/projects', $attributes)->assertRedirect('projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }
}
```

```bash
php artisan make:test ProjectsTest
vendor/bin/phpunit tests/Feature/ProjectsTest.php --filter '/::a_user_can_create_a_project$/'
vendor/bin/phpunit tests/Feature/ProjectsTest.php --filter a_user_can_create_a_project
```

> phpunit.xml

```xml
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

## 03. [Testing Request Validation](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/3)

> We haven't yet written any request validation logic. As before, let's use a TDD approach for specifying each validation requirement.

### Note

```bash
alias pf="vendor/bin/phpunit --filter"
pf a_project_requires_a_title
pf ProjectsTest
```

```bash
php artisan make:factory ProjectFactory --model="App\Project"
```

```php
factory('App\Project')->make();
factory('App\Project')->make(['title' => '']);
factory('App\Project')->raw(['title' => '']);
```

## 04. [Model Tests](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/4)

> We must next ensure that a user can visit any project page. Though we should start with a feature test, this episode will provide a nice opportunity to pause and drop down a level to a model test.

### Note

```bash
php artisan make:test ProjectTest --unit
```

```php
factory('App\Project', 5)->create();
```

> resources\views\projects\index.blade.php

```php
@forelse ($projects as $project)
    <li><a href="{{ $project->path() }}">{{ $project->title }}</li>
@empty
    <li>No projects yet.</li>
@endforelse
```

## 05. [A Project Requires An Owner](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/5)

> It's true that we can now create and persist projects to the database, but they aren't currently associated with any user. This isn't practical. To fix this, we'll write a test to confirm that the authenticated user is always assigned as the owner of any new project that is created during their session.

### Note

```bash
php artisan make:auth
```

> database\migrations\2019_02_26_100953_create_projects_table.php

```php
$table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
```

> database\factories\ProjectFactory.php

```php
[
    'owner_id' => function () {
        return factory(App\User::class)->create()->id;
    }
]
```

> tests\Feature\ProjectsTest.php

```php
$this->actingAs(factory('App\User')->create());
```

> tests\Unit\UserTest.php

```php
use Illuminate\Database\Eloquent\Collection;

$this->assertInstanceOf(Collection::class, $user->projects);
```

## 06. [Scoping Projects](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/6)

> In this episode, we'll continue tweaking which projects are displayed to the user. We'll also begin implementing the appropriate page authorization.

### Note

> tests\Feature\ProjectsTest.php

```php
$this->be(factory('App\User')->create());

$this->get($project->path())->assertStatus(403);
```

> routes\web.php

```php
Route::group(['middleware' => 'auth'], function () {
    Route::get('/projects', 'ProjectsController@index');
});
```

> app\Http\Controllers\ProjectsController.php

```php
if (auth()->user()->isNot($project->owner)) {
    abort(403);
}
```

## 07. [The Create Project View](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/7)

> We already have the necessary logic to persist new projects, however, we haven't yet created the "create project" page, itself. Let's take care of that quickly in this episode.

## 08. [Prepping the Frontend](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/8)

> Before I begin writing CSS, we first need to set the stage. We'll begin by pulling in all necessary npm dependencies and configuring Tailwind compilation with Laravel Mix.

### Note

#### Tailwind

```bash
# 1. Install Tailwind via npm
npm install tailwindcss --save-dev
# 2. Create a Tailwind config file
npx tailwind init
```

```bash
npm install laravel-mix-tailwind --save-dev
```

> webpack.mix.js

```js
require('laravel-mix-tailwind');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .tailwind();
```

### Reference

- [Installation - Tailwind CSS](https://tailwindcss.com/docs/installation/)

- [JeffreyWay/laravel-mix-tailwind: mix.tailwind()](https://github.com/JeffreyWay/laravel-mix-tailwind)

<!-- - [Tailwind 是什么？ | Tailwind CSS 中文网](https://www.tailwindcss.cn/docs/what-is-tailwind/) -->

## 09. [Go Go Gadget Tailwind](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/9)

> In this episode, we'll leverage [Tailwind](http://tailwindcss.com/) to begin constructing the Birdboard UI.

### Note

> resources\views\projects\index.blade.php

```html
<div>{{ Illuminate\Support\Str::limit($project->description) }}</div>

<div>{{ str_limit($project->description) }}</div>
```

### Reference

- [Birdboard (stage)](https://marvelapp.com/4h093if/screen/47239103)

## 10. [Grid Spacing and Card Tweaks](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/10)

> Let's continue working on the CSS for the projects dashboard. This will give us an opportunity to discuss grid spacing, and how we can use a combination of padding and negative margins to perfectly align our columns.

### Note

> resources\sass\app.scss

```sass
.button {
    @apply bg-blue text-white no-underline rounded-lg text-sm py-2 px-5;
    box-shadow: 0 2px 7px 0 #b0eaff;
}
```

## 11. [Styling the Project Page](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/11)

> Before we jump back to PHP, let's write one more lesson's worth of CSS. Specifically, we'll get the single project page up and running.

## 12. [A Project Can Have Tasks](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/12)

> We've added a section to our project page for tasks, but that functionality doesn't yet exist. It sounds like we have our next step.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/0533ad74b757f8447b6351ad62a5cd4bed569d86).

### Note

```bash
php artisan make:model -m Task
```

> tests\TestCase.php

```php
public function signIn($user = null)
{
    $this->actingAs($user ?: factory('App\User')->create());
}
```

> tests\Unit\ProjectTest.php

```php
/** @test */
public function it_can_add_a_task()
{
    $project = factory('App\Project')->create();
    
    $task = $project->addTask('Test task');

    $this->assertCount(1, $project->tasks);
    $this->assertTrue($project->tasks->contains($task));
}
```

> tests\Feature\ProjectTasksTest.php

```php
$project = auth()->user()->projects()->create(
    factory(Project::class)->raw()
);
```

## 13. [Task UI Updates](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/13)

> The next step is to update your project page UI to allow for displaying and adding new tasks.

### Note

> tests\Feature\ProjectTasksTest.php

```php
# two different name
/** @test */
public function only_the_owner_of_a_project_may_add_tasks()
public function adding_a_task_if_you_are_not_the_project_owner()

$this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
```

## 14. [Task UI Updates: Part 2](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/14)

> In this episode, we'll wrap every task within a form so that we may easily update its description or completion status with the click of a button.

> View the source code for this episode on [GitHub](https://github.com/laracasts/birdboard/commit/10c753148feec2430ecd2d2177c64d361cc3d343).

### Note

```bash
php artisan migrate:rollback
php artisan migrate
```

> resources\views\projects\show.blade.php

```php
<input type="checkbox" name="completed" onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>
```

> tests\Feature\ManageProjectsTest.php

```php
$response = $this->post('/projects', $attributes);
$response->assertRedirect(Project::where($attributes)->first()->path());
```

> database\factories\TaskFactory.php

```php
'project_id' => factory(\App\Project::class)
// 'project_id' => function () {
//     return factory(\App\Project::class)->create()->id;
// }
```

> database\migrations\2019_03_01_074823_create_tasks_table.php

```php
$table->timestamp('completed')->default(false);
$table->boolean('completed')->default(false);
```

## 15. [Touch It](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/15)

> I'd like to sort all projects in our dashboard according to those that have been most recently updated. This means, when you add or modify a task within a project, we need to touch the parent project's `updated_at` timestamp in the process. Let's learn how in this episode.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/2192501f311626560eaf55362e55cde5542abb05).

### Note

> app\Task.php

```php
protected $touches = ['project'];

public function project()
{
    return $this->belongsTo(Project::class);
}
```

## 16. [Notes and Policies](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/16)

> Next up, we need to make the "General Notes" section of the project page dynamic. As always, we'll use tests to drive this new update. When finished, we'll also switch over to using dedicated authorization policy classes.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/40afb9c6138578ee22623d3806d398d84fcf1a1b).

### Note

```bash
php artisan make:policy ProjectPolicy
```

> app\Policies\ProjectPolicy.php

```php
public function update(User $user, Project $project)
{
    return $user->is($project->owner);
}
```

> app\Providers\AuthServiceProvider.php

```php
protected $policies = [
    'App\Project' => 'App\Policies\ProjectPolicy',
];
```

> app\Http\Controllers\ProjectsController.php

```php
$this->authorize('update', $project);
```

### Reference

> If you want to apply the fixes to the forms that Jeff has applied "behind the scenes", you'll find all the changes in [this commit](https://github.com/laracasts/birdboard/commit/0ac821b456e599a9c7a8ff3ec7327202c88c6516).

## 17. [Improve Test Arrangements With Factory Classes](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/17)

> Let's take a moment to improve the structure of our tests. Have you noticed that, time and time again, we follow a similar pattern when arranging the world for a test? Instead, let's refactor this code into a fluent factory class to save time. To do this, we'll use a technique that I first learned from John Bonaccorsi in his [Tidy up Your Tests with Class-Based Model Factories](https://tighten.co/blog/tidy-up-your-tests-with-class-based-model-factories) article. I encourage you to give it a read if you'd like to learn more.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/9b18bb34afafbe61116a43d407feadf47be75680).

### Note

> tests\Setup\ProjectFactory.php

```php
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
        $project = factory(Project::class)->create([
            'owner_id' => $this->user ?? factory(User::class)
        ]);

        factory(Task::class, $this->tasksCount)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }
}
```

> tests\Feature\ProjectTasksTest.php

```php
use Tests\Setup\ProjectFactory;

$project = app(ProjectFactory::class)
    // ->ownedBy($this->signIn())
    ->withTasks(1)
    ->create();

$this->actingAs($project->owner)
// $this
    ->patch($project->tasks[0]->path(), [
    'body' => 'changed',
    'completed' => true
]);
```

```php
use Facades\Tests\Setup\ProjectFactory;

$project = ProjectFactory::withTasks(1)->create();
```

### Reference

- [Tidy up Your Tests with Class-Based Model Factories | Tighten](https://tighten.co/blog/tidy-up-your-tests-with-class-based-model-factories)

## 18. [Reduce Form Duplication](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/18)

> In this lesson, we'll add a form to update an existing project. But in the process, we'll review how to reduce duplication in the `create` and `edit` views by extracting a reusable partial.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/1320ddc80709341708222e3ee330eaa96df7ba61).

### Note

> tests\Feature\ManageProjectsTest.php

```php
$this->get($project->path() . '/edit')->assertOk();
```

> resources\views\projects\create.blade.php

```php
@include ('projects.form', [
    'project' => new App\Project,
    'buttonText' => 'Create Project'
])
```

> resources\views\projects\form.blade.php

```php
@if ($errors->any())
    <div class="field mt-6">
        @foreach ($errors->all() as $error)
            <li class="text-sm text-red">{{ $error }}</li>
        @endforeach
    </div>
@endif
```

## 19. [Sometimes Validation With Form Requests](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/19)

> We'll begin this episode by addressing a small regression that was introduced in the previous episode: we can no longer update the general notes without triggering a validation error. While of course we'll review the easy solution to this issue, we'll additionally discuss a recent user comment related to the pros and cons of extracting a form request class. Would it make the code cleaner or better? You can look forward to lots of fun tips in this episode.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/47adf5523b17c4993d337a0ad15f4adfcba681e8).

### Note

```bash
php artsian make:request UpdateProjectRequest
```

> app\Http\Requests\UpdateProjectRequest.php

```php
<?php

namespace App\Http\Requests;

use App\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return Gate::allows('update', $this->route('project'));
        return Gate::allows('update', $this->project());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|required', 
            'description' => 'sometimes|required',
            'notes' => 'nullable'
        ];
    }

    public function project()
    {
        // return $this->route('project');

        // dd(Project::findOrFail($this->route('project')));
        // dd($this->route('project'));

        // use this `Project::findOrFail($this->route('project'))` will cann't use `public function update(UpdateProjectRequest $request, Project $project)` in controller, if, 403 will response.
        // because if in the parameters has `Project $project`,
        // `Project::findOrFail($this->route('project'))` will return array
        // if not, `$this->route('project')` will be "1" (the id of table projects)
        // and `Project::findOrFail($this->route('project'))` will be an object of database collection
        return Project::findOrFail($this->route('project'));
    }

    public function save()
    {
        // $this->project()->update($this->validated());

        // $project = $this->project();

        // $project->update($this->validated());

        // return $project;

        return tap($this->project())->update($this->validated());
    }
}
```

> app\Http\Controllers\ProjectsController.php

>> 1.validate and update in one class

```php
<?php
public function update(Project $project)
{
    $this->authorize('update', $project);     
    $project->update($this->validateRequest());
    return redirect($project->path());
}

protected function validateRequest()
{
    return request()->validate([
        'title' => 'sometimes|required', 
        'description' => 'sometimes|required',
        'notes' => 'nullable'
    ]);
}
```

>> 2.use `UpdateProjectRequest` validate

```php
<?php
public function update(UpdateProjectRequest $request, Project $project)
{
    $project->update($request->validated());
    return redirect($project->path());
}
```

>> 3.use `UpdateProjectRequest` validate and save

```php
<?php
// public function update(UpdateProjectRequest $request, Project $project)
// public function update(UpdateProjectRequest $request)
public function update(UpdateProjectRequest $form)
{    
    // $request->save();

    // return redirect($project->path());
    // return redirect($request->project()->path());
    return redirect($form->save()->path());
}
```

## 20. [Project Activity Feeds](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/20)

> Let's move on to our next feature: every project generates an activity feed.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/1a0bb81b22606ce386eb9e7a33fdb02e74119ae3).

### Note

```bash
php artisan make:observer ProjectObserver --model=Project
```

> app\Providers\AppServiceProvider.php

```php
public function boot()
{
    Project::observe(ProjectObserver::class);
}
```

> routes\web.php

```php
// \App\Project::created(function ($project) {
//     \App\Activity::create([
//         'project_id' => $project->id,
//         'description' => 'Created'
//     ]);
// });
```

## 21. [Project Activity Feeds: Part 2](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/21)

> Let's continue working on the project activity feature. We should additionally record activity when a task is created or completed. Let's take care of that in this episode.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/1ab3d0663eb7188396810c804405c2a15f8d5389).

### Note

> app\Task.php

```php
protected $casts = [
    'completed' => 'boolean'
];

protected static function boot()
{
    parent::boot();

    static::created(function ($task) {
        $task->project->recordActivity('created_task');
    });

    // static::updated(function ($task) {
    //     if (! $task->completed) return;
    //     $task->project->recordActivity('completed_task');
    // });
}

public function complete()
{
    $this->update(['completed' => true]);

    $this->project->recordActivity('completed_task');
}
```

> tests\Unit\TaskTest.php

```php
/** @test */
public function it_can_be_completed()
{
    $task = factory(Task::class)->create();

    $this->assertFalse($task->completed);
    
    $task->complete();

    // $this->assertEquals(true, $task->fresh()->completed);
    $this->assertTrue($task->fresh()->completed);
}
```

## 22. [Project Activity Feeds: Part 3](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/22)

> Before moving on to the larger activity feed refactor, let's first make a few small tweaks to clean things up.

> View the completed source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/d2e4e82eb0855483fcd6437d0caceed2a1eaf9b6).

### Note

> app\Project.php

```php
public function recordActivity($description)
{
    // Activity::create([
    //     'project_id' => $this->id,
    //     'description' => $description
    // ]);

    $this->activity()->create(compact('description'));
}
```

> app\Http\Controllers\ProjectTasksController.php

```php
$method = request('completed') ? 'complete' : 'incomplete';
// $task->{$method}();
$task->$method();
```

## 23. [Project Activity Feeds: Cleanup](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/23)

> We have just a few loose ends to wrap up, before finally viewing the project's activity feed in the browser.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/50322ddffdd5e71c241e7e816553902b0abe639d).

### Note

> tests\Feature\TriggerActivityTest.php

```php
/** @test */
public function incompleting_a_task()
{
    $project = ProjectFactory::withTasks(1)->create();

    $this->actingAs($project->owner)
        ->patch($project->tasks[0]->path(), [
            'body' => 'foobar',
            'completed' => true
        ]);

    $this->assertCount(3, $project->activity);

    $this->patch($project->tasks[0]->path(), [
        'body' => 'foobar',
        'completed' => false
    ]);

    // dd($project->fresh()->activity->toArray());
    // $project = $project->fresh();
    $project->refresh();

    // $this->assertCount(4, $project->fresh()->activity);
    $this->assertCount(4, $project->activity);

    // $this->assertEquals('incompleted_task', $project->fresh()->activity->last()->description);
    $this->assertEquals('incompleted_task', $project->activity->last()->description);
}
```

> app\TriggersActivity.php

```php
<?php
/**
 * if use it currently, the tests will not work.
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
```

### Reference

- [Booting Eloquent Model Traits | The Blog | archybold.com](https://www.archybold.com/blog/post/booting-eloquent-model-traits)

## 24. [Rendering Activity With Polymorphism](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/24)

> It's time to render a project's activity feed on the page. This will give us an opportunity to review how to use polymorphism to load the appropriate view, based on the description type of the activity.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/c2bc7499a4cbca265ed393f456f0752c623dfb02).

### Note

> resources\views\projects\activity\card.blade.php

```php
@foreach ($project->activity as $activity)
    <li class="{{ $loop->last ? '' : 'mb-1' }}">                                
        @include ("projects.activity.{$activity->description}")
        <span class="text-grey">{{ $activity->created_at->diffForHumans(null, true) }}</span>
    </li>
@endforeach
```

## 25. [The Subject of the Activity](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/25)

> Now that we're successfully displaying a project's activity feed on the page, I'd like to provide more information for each update. For example, rather than the message, "You completed a task", it should probably include the name of the task: 'You completed "Finish Lesson"'. This presents a problem because, when we record activity, we don't yet include any reference to the subject. Let's fix that in this episode.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/baa2bd53a1456686e75f4c24b523df7c7ef0da81).

### Note

> tests\Feature\TriggerActivityTest.php

```php
tap($project->activity->last(), function ($activity) {
    // dd($activity->toArray());
    $this->assertEquals('created_task', $activity->description);        
    $this->assertInstanceOf('App\Task', $activity->subject);
    $this->assertEquals('Some task', $activity->subject->body);        
});
```

> database\migrations\2019_03_06_062348_create_activities_table.php

```php
// $table->morphs('subject');
$table->nullableMorphs('subject');
// $table->unsignedInteger('subject_id'); // 9
// $table->string('subject_type');  // App\Task
```

> app\Task.php

```php
public function activity()
{
    return $this->morphMany(Activity::class, 'subject')->latest();
}
```

> app\Activity.php

```php
public function subject()
{
    return $this->morphTo();
}
```

## 26. [Recording Model Changes](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/26)

> Our next job is to track which attributes have changed when a model is updated. This will allow us to record, for example, that the user changed the title of the project from "Test Project" to "Real Project".

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/0212f60a73a1b110a4dbd5c4e041918fb2126e53).

### Note

> app\Observers\ProjectObserver.php

```php
public function updating(Project $project)
{
    $project->old = $project->getOriginal();
}
```

> app\Project.php

```php
public function recordActivity($description)
{
    $this->activity()->create([
        'description' => $description,
        'changes' => $this->activityChanges($description)
    ]);
}

public function activityChanges($description)
{
    if ($description === 'updated') {
        return [
            'before' => array_except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
            'after' => array_except($this->getChanges(), 'updated_at')
        ];
    }
}
```

> tests\Feature\TriggerActivityTest.php

```php
/** @test */
public function updating_a_project()
{
    $project = ProjectFactory::create();

    $originalTitle = $project->title;

    $project->update(['title' => 'Changed']);

    $this->assertCount(2, $project->activity);

    tap($project->activity->last(), function ($activity) use ($originalTitle) {
        $this->assertEquals('updated', $activity->description);

        $expected = [
            'before' => ['title' => $originalTitle],
            'after' => ['title' => 'Changed']
        ];
        $this->assertEquals($expected, $activity->changes);
    });
}
```

## 27. [Get Into The Refactor Flow](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/27)

> In this episode, we'll refactor all "activity" functionality to a reusable trait. As you'll see, we'll quickly fall into a rapid refactor flow: make a small tweak, run the tests, see green, make another small tweak, rinse and repeat.

### Note

> app\RecordsActivity.php

```php  
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
        return ['created', 'updated', 'deleted'];
    }

    public function recordActivity($description)
    {
        $this->activity()->create([
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
```

## 28. [Tweaking the Activity Card](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/28)

> Let's work on tweaking the output of the activity card in this lesson. I'd like it to show which fields were updated, and who performed the update.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/ad4611794b0e602e5cf47d165e7af54bb93d2d46).

### Note

```bash
$ php artisan migrate:refresh
```

> tests\Unit\ActivityTest.php

```php
/** @test */
public function it_has_a_user()
{
    $user = $this->signIn();

    // $project = factory(Project::class)->create();
    $project = ProjectFactory::ownedBy($user)->create();

    // $this->assertInstanceOf(User::class, $project->activity->first()->user);

    $this->assertEquals($user->id, $project->activity->first->user->id);
}
```

## 29. [Authorized Users Can Delete Projects](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/29)

> We have an easy one this episode: users who have the proper permissions may delete or archive projects. Let's knock that out now, before moving on to something more exciting in the next episode.

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/a9131be0775430bbad1dd81447398ad4266fe918).


### Note

> tests\Feature\ManageProjectsTest.php

```php
$this->assertDatabaseMissing('projects', $project->only('id'));
```

> routes\web.php

```php
Route::resource('projects', 'ProjectsController');
```

```bash
$ php artisan route:list
```

## 30. [Layered Tests and Invitations](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/30)

> As we move on to inviting users to projects, this will give us a nice opportunity to discuss layered tests. In this episode, we'll write the same test name at two different levels: from the outside-in (feature), and at a lower level (unit).

> View the source code for this episode [on GitHub](https://github.com/laracasts/birdboard/commit/fdd4a1fab73f57add3105a6c368f32a2eb296e47).

### Note

> tests\Feature\InvitationsTest.php

```php
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

    $this->post(action('ProjectTasksController@store', $project), $task = ['body' => 'Foo task']);

    $this->assertDatabaseHas('tasks', $task);
}
```

> tests\Unit\ProjectTest.php

```php
/** @test */
public function it_can_invite_a_user()
{
    $project = factory('App\Project')->create();
    
    $project->invite($user = factory(\App\User::class)->create());

    $this->assertTrue($project->members->contains($user));
}
```

> app\Project.php

```php
public function invite(User $user)
{
    return $this->members()->attach($user);
}

public function members()
{
    return $this->belongsToMany(User::class, 'project_members');
}
```

> app\Policies\ProjectPolicy.php

```php
public function update(User $user, Project $project)
{
    return $user->is($project->owner) || $project->members->contains($user);
}
```

## References

### [Testing](https://laravel.com/docs/5.8/testing)

### [blade](https://laravel.com/docs/5.8/blade)

### [factory](https://laravel.com/docs/5.8/database-testing)

### [migrations](https://laravel.com/docs/5.8/migrations)

### [mix](https://laravel.com/docs/5.8/mix)

### [eloquent-relationships](https://laravel.com/docs/5.8/eloquent-relationships)

### [touch](https://laravel.com/docs/5.8/eloquent-relationships#touching-parent-timestamps)

### [policy](https://laravel.com/docs/5.8/authorization#creating-policies)

### [validation](https://laravel.com/docs/5.8/validation)

### [Form Request Validation](https://laravel.com/docs/5.8/validation#form-request-validation)

### [Facades](https://laravel.com/docs/5.8/facades)

### [Gate](https://laravel.com/docs/5.8/authorization#gates)

### [Observer](https://laravel.com/docs/5.8/eloquent#observers)

### [casts](https://laravel.com/docs/5.8/eloquent-mutators#attribute-casting)

### [polymorphic-relationships](https://laravel.com/docs/5.8/eloquent-relationships#polymorphic-relationships)

## [title](url)

> 

### Note

### Reference

## References
