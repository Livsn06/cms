<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageManagementController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('admin/package/PackageManagement', [
            'packages' => Package::latest()->orderBy('created_at', 'desc')->get(),
        ]);
    }
}
