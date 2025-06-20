<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'id' => 1,
                'title' => 'First Blog',
                'description' => "It's my first blog",
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'title' => 'Second Blog',
                'description' => "It's my second blog",
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'title' => 'Third Blog',
                'description' => "It's my third blog",
                'is_active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // For your current BlogSeeder with just 3 records:

        // Your original foreach ($blogs as $blog) { Blog::updateOrCreate(['id' => $blog['id']], $blog); } is perfectly fine and readable. The overhead is negligible.
        // For larger datasets (hundreds to thousands+):

        // If you always seed into an empty table (e.g., after migrate:fresh), DB::table('blogs')->insert($blogs); is the most efficient.
        // If you need to update existing records while also inserting new ones, DB::table('blogs')->upsert(...) is the most efficient and recommended approach.
        // foreach ($blogs as $blog) {
        //     Blog::updateOrCreate(['id' => $blog['id']], $blog);
        // }

        // DB::table('blogs')->insert($blogs);

        DB::table('blogs')->upsert(
            $blogs,
            ['id'], // Columns to use as unique identifiers (primary key/unique index)
            ['title', 'description', 'is_active', 'created_by', 'updated_by', 'updated_at'] // Columns to update if a match is found
        );
    }
}
