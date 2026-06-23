<?php

namespace App\Services;

use App\Models\Category;

class TelegramService
{
    public function detectCategory(string $text, string $type): ?Category
    {
        $text = strtolower($text);

        return Category::query()
            ->where('type', $type)
            ->get()
            ->first(function ($category) use ($text) {
                return str_contains(
                    $text,
                    strtolower($category->name)
                );
            });
    }
}
