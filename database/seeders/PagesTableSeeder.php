<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use BaseCms\Models\Page;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => '/home',
            'created_by' => 'System',
        ]);

        Page::create([
            'title' => 'About',
            'slug' => '/about',
            'created_by' => 'System',
        ]);

        Page::create([
            'title' => 'Contact',
            'slug' => '/contact',
            'created_by' => 'System',
        ]);
    }
}
