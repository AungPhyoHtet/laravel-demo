<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(): View
    {
        return view('admin.index', [
            'userCount' => User::count(),
            'ideas' => Idea::with('user')->latest()->paginate(10),
        ]);
    }
}
