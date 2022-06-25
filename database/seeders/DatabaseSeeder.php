<?php

namespace Database\Seeders;

use App\Http\Controllers\ProductController;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run()
    {
        $admin = new User();
        $admin->name = "admin";
        $admin->email = "admin@mail.com";
        $admin->password = Hash::make("pass1234");
        $admin->privileged = true;
        $admin->save();
    }
}
