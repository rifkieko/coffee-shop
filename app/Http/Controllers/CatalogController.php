<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $menuItems = MenuItem::with('category')
            ->when(!$search, function ($query) {
                $query->active()->inStock();
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($search, function ($query) {
                $query->orderByRaw('CASE WHEN is_active AND stock > 0 THEN 0 ELSE 1 END');
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
        $limit = (int) $request->integer('limit', 150);
        $limit = max(10, min($limit, 300));
        $like = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

        if ($q === '' || mb_strlen($q) < 1) {
            return response()->json(['items' => []]);
        }

        $items = MenuItem::with('category')
            ->where(function ($query) use ($q, $like) {
                $query->where('name', $like, "%{$q}%")
                    ->orWhere('slug', $like, "%{$q}%")
                    ->orWhere('description', $like, "%{$q}%")
                    ->orWhereHas('category', function ($cat) use ($q, $like) {
                        $cat->where('name', $like, "%{$q}%");
                    });
            })
            ->orderByRaw('CASE WHEN is_active THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->limit($limit)
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
