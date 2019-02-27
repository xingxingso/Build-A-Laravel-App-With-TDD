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

## [title](url)

> 

### Note

### Reference
