<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->get();

        $packages = $packages->map(function ($package) {
            return [
                'id' => $package->id,
                'name' => $package->name,
                'image' => $package->image ? asset('storage/' . $package->image) : null,
                'price' => $package->price,
                'description' => $package->description,
                'created_at' => $package->created_at,
                'updated_at' => $package->updated_at
            ];
        });
        return Inertia::render('client/home/Landing', compact('packages'));
    }
}
