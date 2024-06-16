<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }   
    public function isAdmin()
{
    return $this->role->name === 'admin';
}
    public function bugs()
    {
        return $this->belongsToMany(Bug::class, 'bug_developer', 'developer_id', 'bug_id');
    }

    public function assignedBugs()
    {
        return $this->hasMany(Bug::class, 'tester_id');
    }
    public function assignedTster()
    {
        return $this->hasMany(Task::class,'tester_id');
    }
    public function assignedDeveloper()
    {
        return $this->hasMany(Task::class,'developer_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
