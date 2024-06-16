<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['title','status','description','project_id','user_id','date','read_at'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }
}
