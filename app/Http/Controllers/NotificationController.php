<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index($developerId)
    {
        $data=Notification::with('user','project')
        ->wheres('user',function($query) use ($developerId)
        {
            $query->where('id',$developerId);
        })->get();

        return response()->json($data);
    }
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read_at = now();
        $notification->save();

        return response()->json(['success' => true]);
    }
}
