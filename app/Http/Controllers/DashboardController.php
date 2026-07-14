<?php

namespace App\Http\Controllers;

use App\Models\Kategori;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $categories = Kategori::all();

        return view('pages.admin.dashboard', compact('categories'));
    }
}
