<?php

namespace App\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class HeaderComposer
{
    public function compose(View $view): void
    {
        // @todo store to cache, so whenever reorder / create / update / delete category => clear cache
        $menuCategories = Category::query()
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->with(['children' => function ($q) {
                $q->orderBy('sort');
            }])
            ->get();

        $view->with('menuCategories', $menuCategories);
    }
}
