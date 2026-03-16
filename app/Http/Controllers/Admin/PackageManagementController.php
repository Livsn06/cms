<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PackageManagementController extends Controller
{


    // Package Management INDEX
    public function index(): \Inertia\Response
    {
        $packagesData = Package::latest()->get()->map(function ($package) {
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

        return Inertia::render('admin/package/PackageManagement', compact('packagesData'));
    }


    //
    //
    //
    // CREATE PACKAGE INDEX

    public function create()
    {
        return Inertia::render('admin/package/CreatePackage');
    }



    //
    //
    //
    // STORE PACKAGE

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fields = $request->only([
            'name',
            'price',
            'description',
            'image',
        ]);

        DB::transaction(function () use ($fields) {
            if (isset($fields['image']) && $fields['image'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $fields['image']->store('packages', 'public');
                $fields['image'] = $path;
            } else {
                $fields['image'] = null;
            }

            Package::create($fields);
        });

        return back()->with('success', 'Package created successfully!');
    }




    //
    //
    //
    // UPDATE PACKAGE


    public function edit(Package $package)
    {
        $package = [
            'id' => $package->id,
            'name' => $package->name,
            'old_image' => $package->image ? asset('storage/' . $package->image) : null,
            'price' => $package->price,
            'description' => $package->description
        ];
        return Inertia::render('admin/package/EditPackage', compact('package'));
    }



    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'old_image' => 'nullable|string',
        ]);



        DB::transaction(function () use ($request, $package) {
            $path = null;
            if ($request->file('image')) {
                if ($package->image) Storage::disk('public')->delete($package->image);
                $path = $request->file('image')->store('packages', 'public');
            } elseif (!$request->old_image) {
                if ($package->image) Storage::disk('public')->delete($package->image);
                $path = null;
            } else {
                $path = $package->image;
            }

            $fields = $request->only([
                'name',
                'price',
                'description',
            ]);

            $package->image = $path;
            $package->name = $fields['name'];
            $package->price = $fields['price'];
            $package->description = $fields['description'];
            $package->save();
        });

        return back()->with('success', 'Package updated successfully!');
    }





    //
    //
    //
    // SHOW PACKAGE

    public function show(Package $package)
    {
        $package = [
            'id' => $package->id,
            'name' => $package->name,
            'image' => $package->image ? asset('storage/' . $package->image) : null,
            'price' => $package->price,
            'description' => $package->description,
            'created_at' => $package->created_at,
            'updated_at' => $package->updated_at
        ];

        return Inertia::render('admin/package/ShowPackage', compact('package'));
    }



    //
    //
    //
    // DELETE PACKAGE

    public function destroy(Package $package)
    {
        if ($package->image) Storage::disk('public')->delete($package->image);
        $package->delete();
        return back()->with('success', 'Package deleted successfully!');
    }
}
