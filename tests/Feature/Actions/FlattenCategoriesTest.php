<?php

use App\Actions\FlattenCategories;
use App\Models\Category;
use App\Models\CategoryGroup;

test("handle", function () {
    $categoryGroups = CategoryGroup::factory(1)->raw([
        'categories' => Category::factory(1)->raw(),
    ]);

    $result = (new FlattenCategories)->handle($categoryGroups);

    expect($result)->toEqual(collect($categoryGroups[0]['categories']));
});
