<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Technology', 'Music', 'Workshop', 'Arts', 'Sports', 'Business'];
        foreach ($categories as $name) {
            Category::create(['name' => $name, 'slug' => Str::slug($name)]);
        }

        $tags = ['Free', 'Paid', 'Online', 'In-Person', 'Networking', 'Beginner', 'Advanced'];
        foreach ($tags as $name) {
            Tag::create(['name' => $name, 'slug' => Str::slug($name)]);
        }
    }
}
