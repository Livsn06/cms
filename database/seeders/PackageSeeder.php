<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Buffet Basic',
                'description' => 'This is a basic package',
                'price' => 10.99,
            ],
            [
                'name' => 'Weeding Special Standard',
                'description' => 'This is a standard package',
                'price' => 19.99,
            ],
            [
                'name' => 'Premium Summer Buffet',
                'description' => 'This is a premium package',
                'price' => 29.99,
            ],
        ];

        DB::transaction(function () use ($data) {
            foreach ($data as $item) {
                Package::create($item);
            }
        });
    }
}
