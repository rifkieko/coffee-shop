<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $menuItems = MenuItem::with('category')
            ->active()
            ->inStock()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderByRaw('COALESCE(category_id, 0)')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $menuGroups = $menuItems->getCollection()
            ->groupBy(function (MenuItem $item) {
                return optional($item->category)->id ?? 'uncategorized';
            })
            ->map(function ($items) {
                /** @var \Illuminate\Support\Collection<int, MenuItem> $items */
                $category = $items->first()->category;

                return [
                    'category' => $category,
                    'items' => $items,
                ];
            })
            ->values();

        return view('catalog.index', [
            'menuGroups' => $menuGroups,
            'menuItems' => $menuItems,
            'search' => $search,
        ]);
    }
}
