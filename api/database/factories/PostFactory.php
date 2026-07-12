<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);
        $status = fake()->randomElement(['draft', 'published']);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(8, true),
            'excerpt' => fake()->sentence(),
            'status' => $status,
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'featured_image_id' => null,
            'published_at' => $status === 'published' ? fake()->dateTimeThisYear() : null,
            'meta_title' => $title,
            'meta_description' => fake()->sentence(),
        ];
    }
}
