<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends CustomRepository
{
    public function getCategory($catId): ?Category
    {
        return Category::find($catId);
    }

    public function getCategoryNameById($catId): ?String
    {
        return Category::find($catId)->value('name');
    }

    public function getCategoryIdByName(string $categoryName): ?int
    {
        return Category::where('name', $categoryName)->value('id');
    }
}
