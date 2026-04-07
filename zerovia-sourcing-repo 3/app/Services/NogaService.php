<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NogaService
{
    /**
     * Search NOGA taxonomy by keyword or code prefix.
     */
    public function search(string $query, int $limit = 20): Collection
    {
        return $this->all()
            ->filter(function ($entry) use ($query) {
                $q = strtolower(trim($query));
                return str_contains(strtolower($entry['code']), $q)
                    || str_contains(strtolower($entry['de']), $q)
                    || str_contains(strtolower($entry['fr'] ?? ''), $q);
            })
            ->take($limit)
            ->values();
    }

    /**
     * Load full NOGA taxonomy (cached).
     */
    public function all(): Collection
    {
        return Cache::rememberForever('noga_taxonomy', function () {
            $path = database_path('seeders/data/noga_taxonomy.json');
            if (!file_exists($path)) {
                return collect();
            }
            return collect(json_decode(file_get_contents($path), true) ?? []);
        });
    }

    /**
     * Resolve code to label.
     */
    public function label(string $code): string
    {
        $entry = $this->all()->firstWhere('code', $code);

        return $entry['de'] ?? $code;
    }
}
