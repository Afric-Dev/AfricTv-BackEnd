<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.edit-admin', compact('admin'));
    }

    public function index(Request $request)
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();

        return view('admin.admin', compact('admins'));
    }

    public function add()
    {
        return view('admin.add-admin');
    }
}
