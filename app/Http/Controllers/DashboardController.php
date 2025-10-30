<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\ShopTable;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user?->role === UserRole::Admin) {
            return view('admin.dashboard', [
                'menuCount' => MenuItem::count(),
                'categoryCount' => Category::count(),
                'activeTableCount' => ShopTable::where('is_active', true)->count(),
                'pendingOrdersCount' => Order::where('status', OrderStatus::Pending)->count(),
                'latestOrders' => Order::with(['user', 'table'])
                    ->latest()
                    ->limit(5)
                    ->get(),
                'notifications' => $user->notifications()->latest()->limit(8)->get(),
                'unreadNotificationCount' => $user->unreadNotifications()->count(),
            ]);
        }

        return redirect()->route('home');
    }
}
