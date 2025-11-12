<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

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
            ->get();

        $menuGroups = $menuItems
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
            'search' => $search,
        ]);
    }

    public function show(MenuItem $menuItem): View
    {
        return view('catalog.show', [
            'item' => $menuItem->load('category'),
        ]);
    }

    // Live search endpoint: returns lightweight JSON results
    public function lookup(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['items' => []]);
        }

        $items = MenuItem::with('category')
            ->active()
            ->inStock()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', function ($cat) use ($q) {
                        $cat->where('name', 'like', "%{$q}%");
                    });
            })
            ->orderBy('name')
            ->limit(8)
            ->get();

        $results = $items->map(function (MenuItem $item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'image_url' => $item->image_url,
                'category' => optional($item->category)->name,
                'url' => route('catalog.show', $item),
            ];
        });

        return response()->json(['items' => $results]);
    }
}
