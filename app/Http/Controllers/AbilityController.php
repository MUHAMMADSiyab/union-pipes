<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbilityController extends Controller
{
    /**
     * Get user's all abilities(permissions)
     * @return Illuminate\Http\Response 
     */
    public function index()
    {
        return auth()->user()->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->toArray();
    }
}
