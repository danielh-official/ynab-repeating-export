<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Collection;

class FlattenCategories
{
    public function handle(Collection|array $categoryGroups): Collection
    {
        $flattenedCategories = collect();

        if (is_array($categoryGroups)) {
            $categoryGroups = collect($categoryGroups);
        }

        foreach ($categoryGroups as $categoryGroup) {
            $categories = data_get($categoryGroup, 'categories', []);

            foreach ($categories as $category) {
                $flattenedCategories->push($category);
            }
        }

        return $flattenedCategories;
    }
}
