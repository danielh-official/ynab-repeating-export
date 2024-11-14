<?php

declare(strict_types=1);

namespace App\Actions\Sample;

use App\Models\Category;
use App\Models\CategoryGroup;

class GetSampleCategoryGroups
{
    public function handle()
    {
        return collect(CategoryGroup::factory()->count(3)->raw([
            'categories' => Category::factory()->count(3)->raw(),
        ]));
    }
}
