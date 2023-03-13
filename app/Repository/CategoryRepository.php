<?php

namespace App\Repository;

use App\Models\Category;

class CategoryRepository
{
    public static function listing($perPage = 10, $page = 1, $search = null, $orderColumn = null, $orderDirection = null)
    {
        $categories = Category::query()->withCount('articles');

        if ($search) {
            $categories->where('type', 'like', '%' . $search . '%');
        }

        if ($orderColumn && $orderDirection) {
            $categories->orderBy($orderColumn, $orderDirection);
        }

        return $categories->paginate($perPage, ['*'], 'page', $page);
    }

    public static function store(string $type)
    {
        $category = new Category();
        $category->type = $type;
        $category->save();
        return $category;
    }

    public static function update($category, string $type): bool
    {
        $category->type = $type;

        return $category->save();
    }
}
