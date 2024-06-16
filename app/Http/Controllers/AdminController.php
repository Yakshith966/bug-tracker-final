<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getDevelopers()
{
    $developers = User::whereHas('role', function($query) {
        $query->where('name', 'developer');
    })->get();

    return response()->json($developers);
}

public function getTesters()
{
    $testers = User::whereHas('role', function($query) {
        $query->where('name', 'tester');
    })->get();

    return response()->json($testers);
}

}
