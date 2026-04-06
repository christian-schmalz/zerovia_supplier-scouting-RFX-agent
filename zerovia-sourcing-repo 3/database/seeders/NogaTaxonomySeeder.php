<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class NogaTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/noga_taxonomy.json');

        if (!file_exists($path)) {
            $this->command->warn('NOGA taxonomy JSON not found at ' . $path . ' — skipping.');
            return;
        }

        // Clear the cached taxonomy so NogaService reloads fresh data
        Cache::forget('noga_taxonomy');

        $count = count(json_decode(file_get_contents($path), true));
        $this->command->info("✓ NOGA taxonomy cache cleared — {$count} entries available");
    }
}
