<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }
    public function assignedBugs()
    {
        return $this->hasMany(Bug::class, 'tester_id');
    }
    public function assignedTask()
    {
        return $this->hasMany(Task::class, 'project_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'project_id');
    }
}
