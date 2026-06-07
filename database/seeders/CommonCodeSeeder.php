<?php

namespace Database\Seeders;

use App\Models\CommonCode;
use Illuminate\Database\Seeder;

class CommonCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = config('codes.items', []);

        $defaults = [
            'description' => null,
            'sort_order' => 0,
            'is_active' => true,
            'meta' => null,
            'deleted_at' => null,
        ];

        $payload = array_map(
            static fn (array $code) => array_merge($defaults, $code),
            $codes
        );

        CommonCode::query()->upsert(
            $payload,
            ['group_key', 'code'],
            ['label', 'description', 'sort_order', 'is_active', 'meta', 'deleted_at']
        );
    }
}
