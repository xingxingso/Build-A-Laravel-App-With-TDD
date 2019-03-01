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

```scss
.button {
    @apply bg-blue text-white no-underline rounded-lg text-sm py-2 px-5;
    box-shadow: 0 2px 7px 0 #b0eaff;
}
```

## 11. [Styling the Project Page](https://laracasts.com/series/build-a-laravel-app-with-tdd/episodes/11)

> Before we jump back to PHP, let's write one more lesson's worth of CSS. Specifically, we'll get the single project page up and running.

## [title](url)

> 

### Note

### Reference
