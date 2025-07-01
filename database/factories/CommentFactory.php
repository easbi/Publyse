<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Acak antara tipe 'point' atau 'area'
        $type = $this->faker->randomElement(['point', 'area']);
        $position = [];

        if ($type === 'point') {
            $position = [
                'x' => $this->faker->numberBetween(50, 400),
                'y' => $this->faker->numberBetween(50, 600),
            ];
        } else {
            $position = [
                'x' => $this->faker->numberBetween(50, 200),
                'y' => $this->faker->numberBetween(50, 300),
                'width' => $this->faker->numberBetween(100, 200),
                'height' => $this->faker->numberBetween(50, 150),
            ];
        }

        return [
            // Kita akan menyediakan document_id dan user_id saat memanggil factory
            'page_number' => $this->faker->numberBetween(1, 10),
            'type' => $type,
            'position' => json_encode($position), // Simpan sebagai string JSON
            'content' => $this->faker->sentence(10),
            'status' => $this->faker->randomElement(['open', 'done']),
            'parent_id' => null, // Komentar utama tidak punya induk
        ];
    }
}
