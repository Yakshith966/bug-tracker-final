<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','task_images','tester_id','status','developer_id','project_id'];
    public function tester()
    {
        return $this->belongsTo(User::class, 'tester_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
    protected $casts = [
        'task_images' => 'array',
    ];
    
}
