<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function projects()
    {
        // return $this->hasMany(Project::class, 'owner_id')->orderBy('updated_at', 'desc');
        // return $this->hasMany(Project::class, 'owner_id')->orderByDesc('updated_at');
        return $this->hasMany(Project::class, 'owner_id')->latest('updated_at');
    }
}
